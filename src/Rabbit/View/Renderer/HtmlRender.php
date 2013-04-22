<?php
namespace Rabbit\View\Renderer;

use Rabbit\Application\Front;
use Rabbit\View\Exception\ViewRenderException;
use Rabbit\View\View;
use Symfony\Component\HttpFoundation\Request;

class HtmlRender implements RenderInterface {

	protected $args = array();
	private $_config;

	public function __construct($config){
		$this->_config = $config;
		if(isset($config['args']))
			$this->configArgsForView($config['args']);
	}

	private function configArgsForView($args){
		if(is_array($args) || is_object($args))
			foreach($args as $key => $value){
				if(is_string($key))
					$this->$key = $value;
				$this->args[$key] = $value;
			}
	}

	public function render(){

		$request = Front::getInstance()->getRequest();

		$module 		= isset($this->_config['module'])?		$this->_config['module'] 		: $request->get("module");
		$namespace 		= isset($this->_config['namespace'])? 	$this->_config['namespace'] 	: $request->get("namespace");
		$controller 	= isset($this->_config['controller'])? 	$this->_config['controller'] 	: $request->get("controller");
		$action 		= isset($this->_config['action'])?		$this->_config['action'] 		: $request->get("action");

		$prefix = isset($this->_config['prefix'])?	$this->_config['prefix'] : 'phtml';

		$fileURI = RABBIT_PATH_MODULE . DS . ucfirst($module) . DS . 'view' . DS .  $namespace . DS . $controller . DS . $action . '.' .$prefix;

		if(!file_exists($fileURI))
			throw new ViewRenderException(sprintf('Arquivo não encontrado <strong>%s</strong>', $fileURI));

		ob_start();
		require_once $fileURI;
		return  ob_get_clean();
	}

	public function get($key, $default = NULL) {
		return isset($this->args[$key])? $this->args[$key] : $default;
	}

	public function __call($name, $args){
		if($result = View::getHelper($name, $args))
			return $result;
	}

}