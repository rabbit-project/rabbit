<?php

namespace Rabbit\Application;

use Rabbit\Logger\LoggerException;

use Rabbit\Logger\LoggerType;

use Rabbit\Logger\LoggerManager;

use DirectoryIterator;
use Rabbit\Routing\Router;
use Rabbit\Routing\RouterException;
use Rabbit\Service\ServiceLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;

/**
 * Front
 * Classe principal para application do RabbitCMS
 * @author Erick Leão <erickleao@rabbit-cms.com.br>
 */
class Front {
	
	private static $_instance;
	
	/**
	 * @var Router
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
	
	/**
	 * @var multiple:PluginInterface
	 */
	private $_plugins = array();
	
	private $_log;
	
	private function __construct() {
		$this->_log = LoggerManager::getInstance()->getLogger(get_class());
		
		$this->initWhithConfig();
				
		if(!$this->_request)
			$this->_request = Request::createFromGlobals();
		
		if(!$this->_response)
			$this->_response = new Response();
		
		$this->initLogger();
		
		if(!$this->_router)
			$this->_router = new Router($this->_request);		
		
		$this->mappingModules();
		//$this->initPlugin();
		
		if(isset($this->_config["moduleDefault"]))
			$this->_router->setModuleDefault($this->_config["moduleDefault"]);
	}
	
	private function initPlugin() {
		foreach($this->_plugins as $plugin)
			$plugin->execute();
	}
	
	private function initLogger() {
		if(isset($this->_config["loggerManager"])){
			if(isset($this->_config["loggerManager"]["trace"]) && $this->_config["loggerManager"]["trace"])
				LoggerManager::getInstance()->setTrace(true);
				
			if(isset($this->_config["loggerManager"]["export"]))
				LoggerManager::getInstance()->setExportConfig($this->_config["loggerManager"]["export"]);
				
			if(isset($this->_config["loggerManager"]["active"]) && $this->_config["loggerManager"]["active"]){
				LoggerManager::getInstance()->setActive(true);
				if(isset($this->_config["loggerManager"]["nivel"]) && $this->_config["loggerManager"]["nivel"] instanceof LoggerType){
					LoggerManager::getInstance()->setNivelLogger($this->_config["loggerManager"]["nivel"]);
				}
			}
		}
	}
	
	private function initWhithConfig() {
		$fileConfigGlobalURI = RABBIT_PATH_APPLICATION . DS . "config" . DS . "global.config.php";
		$fileConfigLocalURI = RABBIT_PATH_APPLICATION . DS . "config" . DS . "local.config.php";
		
		if(!file_exists($fileConfigGlobalURI))
			throw new ApplicationException(sprintf("Não foi possível encontrar o arquivo de configuração: <strong>%s</strong>", $fileConfigGlobalURI));
		
		$this->_config = include $fileConfigGlobalURI;
		
		if(file_exists($fileConfigLocalURI))
			$this->_config = array_merge($this->_config, include $fileConfigLocalURI);		
		
	}
	
	/**
	 * Mapea todos os módulos
	 */
	private function mappingModules() {
		$dirModules = new DirectoryIterator(RABBIT_PATH_MODULE);
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
					
					if(isset($config["plugins"])){
						$plugins = array();
						
						if(!is_string($config["plugins"]) && !is_array($config["plugins"]))
							throw new ApplicationException(sprintf("A configuração do plugin no modulo %s não é uma String ou um Array", $dirModule->getFilename()));
						
						$plugins = (is_string($config["plugins"]))? array($config["plugins"]) : $config["plugins"];
						
						
						foreach ($plugins as $pluginName){
							$plugin = new $pluginName($this->_request, $this->_response);
							$this->registerPlugin($plugin);
						}
					}
				}
				
				$routerFile = $dirModule->getPathname() . DS . 'router.yml';
				if(!file_exists($routerFile))
					throw new MvcFileNotFoundException(sprintf("Não foi possível encontrar o arquivo: <strong>%s</strong> de roteamento do módulo: <strong>%s</strong>",$routerFile, $dirModule->getPathname()));
				
				$router = Yaml::parse($routerFile);
				$this->addRouters($router);
			}
		}		
	}
	
	/**
	 * @param array $routers
	 * @throws RouterException
	 */
	public function addRouters(array $routers) {
		foreach ($routers as $name => $params){
			if(!isset($params['type'])){
				$clsName = 'Rabbit\Routing\Mapping\Literal';
			}else{
				$clsName = $params['type'];
			}
			
			$defaults = isset($params['defaults'])? $params['defaults'] : array();
			$options = isset($params['options'])? $params['options'] : array();
			
			if(!isset($params['url']))
				throw new RouterException('Não foi definido o parametro "url" de mapeamento');
			
			$this->getRouter()->addMapping($name, new $clsName($params['url'], $defaults, $options));
		}
	}
	
	/**
	 * Registra os Services
	 * @param array $services
	 * @throws ApplicationException
	 */
	private function registerServices(array $services) {
		foreach($services as $servKey => $serv){
			if(ServiceLocator::isRegistred($servKey))
				throw new ApplicationException(sprintf('O servico <strong>%s</strong> já existe o mesmo não pode ser novamente registrado', $servKey));
			
			if(is_array($serv)){
				ServiceLocator::register($servKey, $serv["fn"], $serv["unique"]);
			}else{
				ServiceLocator::register($servKey, $serv);
			}
		}
	}	
		
	/**
	 * Inicia a configuração do sistema
	 * @return Front
	 */
	public static function getInstance() {
		if(!self::$_instance instanceof Front){
			self::$_instance = new self;
		}
		
		return self::$_instance;
	}
	
	public function run() {
		$this->_log->log("Iniciando Front", LoggerType::get("RABBIT"));
		$self = self::getInstance();
		$this->_router->execute();		
		$self->dispatch();
		$this->_log->log("Finalizando Front", LoggerType::get("RABBIT"));
	}
	
	/**
	 * Dispatch
	 */
	public function dispatch(){
		$module 	= ucfirst($this->_request->get("module"));
		$namespace 	= 'Namespaces\\' . ucfirst($this->_request->get("namespace"));
		$controller = ucfirst($this->_request->get("controller")) . "Controller";
		$action 	= $this->_request->get("action") . "Action";
		
		$clsName = $module . '\\' . $namespace . '\Controller\\' . $controller;

		if(!file_exists(str_replace('\\', DS, RABBIT_PATH_MODULE . DS . $clsName . '.php'))){
			throw new ApplicationException(sprintf("Não foi possível encontrar o Controller: <strong>%s</strong>", $clsName));
		}else if($this->_router->getMapped() == null) {
			throw new ApplicationException(sprintf("Roteamento não encontrado"), 404);
		}
		
		$clsI = new $clsName($this->_request, $this->_response);
		$this->initPlugin();		
		$clsI->dispatch($action);
		
		$this->_response->send();
	}
		
	/**
	 * Retorna o Routing
	 * @return Router
	 */
	public function getRouter() {
		return $this->_router;
	}
	
	public function setRouter(Router $router) {
		$this->_router = $router;
	}
	
	public function registerPlugin(PluginInterface $plugin) {
		array_push($this->_plugins, $plugin);
	}
	
	public function getPlugins() {
		return $this->_plugins;
	}

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function setRequest($request)
    {
        $this->_request = $request;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    public function setResponse($response)
    {
        $this->_response = $response;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse()
    {
        return $this->_response;
    }


}