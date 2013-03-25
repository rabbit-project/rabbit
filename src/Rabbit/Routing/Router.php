<?php
namespace Rabbit\Routing;

use Rabbit\Routing\Mapping\RouterMappingAbstract;

use Symfony\Component\HttpFoundation\Request;

class Router {
	
	private $_mappings = array();
	
	// definção dos defaults
	private $_defaults = array(
		"module"		=> "application",
		"namespace"		=> "main",
		"controller"	=> "index",
		"action"		=> "index",
	);

	/**
	 * @var Request
	 */
	private $_request;
	
	/**
	 * @var MappingAbstract
	 */
	private $_router;
	
	public function __construct(Request $request) {
		$this->_request = $request;
	}
	
	public function execute() {
		$hMap = null;

		//$this->_request->attributes->add($this->_defaults);
		
		foreach($this->_mappings as $mapping) {
			if($mapping->match($this->_request)){
				if(!$hMap || ($hMap && ($hMap->getHierarchy() < $mapping->getHierarchy())))
					$hMap = $mapping;
			}
		}
		
		if($hMap){
			$this->_router = $hMap; 
			$this->_request->attributes->add($hMap->getParams());
		}
	}
	
	public function addMapping($name, RouterMappingAbstract $map){
		$this->_mappings[$name] = $map;
	}
	
	public function getMapping($name) {
		if(isset($this->_mappings[$name]))
			return $this->_mappings[$name];
		return; 
	}
	
	public function getModuleDefault() {
		return $this->_defaults["module"];
	}
	
	public function setModuleDefault($name) {
		$this->_defaults["module"] = strtolower($name);
	}
	
	public function getNamespaceDefault() {
		return $this->_defaults["namespace"];
	}
	
	public function setNamespaceDefault($name) {
		$this->_defaults["namespace"] = strtolower($name);
	}
	
	public function getControllerDefault() {
		return $this->_defaults["controller"];
	}
	
	public function setControllerDefault($name) {
		$this->_defaults["controller"] = strtolower($name);
	}
	
	public function getActionDefault() {
		return $this->_defaults["action"];
	}
	
	public function setActionDefault($name) {
		$this->_defaults["action"] = strtolower($name);
	}
	
	/**
	 * @return RouterMappingAbstract
	 */
	public function getMapped() {
		return $this->_router;	
	}
}