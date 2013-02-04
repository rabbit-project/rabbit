<?php

namespace Rabbit\Application;

use Doctrine\Common\Annotations\AnnotationRegistry;

use Rabbit\Service\ServiceLocator;

use Rabbit\Controller\Exception;
use Rabbit\Routing\Router;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;



class Front {
	
	private static $_instance;
	
	/**
	 * @var \Rabbit\Routing\Router
	 */
	private $_router;
	
	/**
	 * @var Request
	 */
	private $_request;
	
	/**
	 * @var Response
	 */
	private $_response;
	
	private $_config = array();
	
	private $_modules = array();
	
	private function __construct() {
		$this->initWhithConfig();
		
		if(!$this->_request)
			$this->_request = Request::createFromGlobals();
		
		if(!$this->_response)
			$this->_response = new Response();
		
		if(!$this->_router)
			$this->_router = new Router($this->_request);
				
		$this->mappingModules();
		
		if(isset($this->_config["moduleDefault"]))
			$this->_router->setModuleDefault($this->_config["moduleDefault"]);
		
		$this->_router->execute();
		
	}
	
	private function initWhithConfig() {
		$fileConfigGlobalURI = RABBIT_PATH_APPLICATION . DS . "config" . DS . "global.config.php";
		
		if(!file_exists($fileConfigGlobalURI))
			throw new Exception(sprintf("Não foi possível encontrar o arquivo de configuração: <strong>%s</strong>", $fileConfigGlobalURI));
		
		$this->_config = include $fileConfigGlobalURI;
	}
	
	/**
	 * Mapea todos os módulos
	 */
	private function mappingModules() {
		$dirModules = new \DirectoryIterator(RABBIT_PATH_MODULE);
		$load = ServiceLocator::getService("Rabbit\Load");
		foreach($dirModules as $dirModule){
			if($dirModule->isDir() && !$dirModule->isDot()){
				// registrando namespace
				$load->add($dirModule->getFilename(), RABBIT_PATH_MODULE);
				
				// recuperando o Module.php
				$clsModule =  $dirModule->getFilename() . "\Module";
				$clsI = new $clsModule();
				
				// verificar se o metodo de configuração existe
				if(method_exists($clsI, "getConfig")){
					// recupera as configurações
					$config = $clsI->getConfig();
					if(isset($config["services"]))
						$this->registerServices($config["services"]);
				}
				
				$routerFile = $dirModule->getPathname() . DS . 'router.yml';
				if(!file_exists($routerFile))
					throw new MvcFileNotFoundException(sprintf("Não foi possível encontrar o arquivo: <strong>%s</strong> de roteamento do módulo: <strong>%s</strong>",$routerFile, $dirModule->getPathname()));
				
				$router = Yaml::parse($routerFile);
				$this->addRouters($router);
			}
		}		
	}
	
	public function addRouters(array $routers) {
		foreach ($routers as $name => $params){
			if(!isset($params['type'])){
				$clsName = 'Rabbit\Routing\Mapping\Literal';
			}else{
				$clsName = $params['type'];
			}
			
			$defaults = isset($params['defaults'])? $params['defaults'] : array();
			$requirements = isset($params['requirements'])? $params['requirements'] : array();
			
			if(!isset($params['url']))
				throw new Rabbit\Routing\Exception('Não foi definido o parametro "url" de mapeamento');
			
			$this->getRouter()->addMapping($name, new $clsName($params['url'], $defaults, $requirements));
		}
	}
	
	/**
	 * Registra os Services
	 * @param array $services
	 * @throws Exception
	 */
	private function registerServices(array $services) {
		foreach($services as $servKey => $serv){
			if(ServiceLocator::isRegistred($servKey))
				throw new Exception(sprintf('O servico <strong>%s</strong> já existe o mesmo não pode ser novamente registrado', $servKey));
			
			if(is_array($serv)){
				ServiceLocator::register($servKey, $serv["fn"], $serv["unique"]);
			}else{
				ServiceLocator::register($servKey, $serv);
			}
		}
	}	
		
	/**
	 * Inicia a configuração do sistema
	 * @return Rabbit\Application
	 */
	public static function getInstance() {
		if(!self::$_instance instanceof Front)
			self::$_instance = new self();
		
		return self::$_instance;
	}
	
	public static function run() {
		self::getInstance()->dispatch();
	}
	
	/**
	 * Dispatch
	 */
	public function dispatch(){
		$module 	= ucfirst($this->_request->get("module"));
		$namespace 	= ucfirst($this->_request->get("namespace"));
		$controller = ucfirst($this->_request->get("controller")) . "Controller";
		$action 	= $this->_request->get("action") . "Action";
		
		$clsName = $module . "\\" . $namespace . "\Controller\\" . $controller;
		$clsI = new $clsName($this->_request, $this->_response);
		$clsI->dispatch($action);
		
		$this->_response->send();
	}
		
	/**
	 * Retorna o Routing
	 * @return \Rabbit\Routing\Router
	 */
	public function getRouter() {
		return $this->_router;
	}
	
	public function setRouter(Router $router) {
		$this->_router = $router;
	}
}