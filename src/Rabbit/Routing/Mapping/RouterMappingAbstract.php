<?php
namespace Rabbit\Routing\Mapping;

abstract class RouterMappingAbstract {
	
	/**
	 * Hierarquia do roteamento
	 * @var int
	 */
	protected $_hierarchy = 0;
	
	/**
	 * Parametros do mapeamento
	 * @var array
	 */
	protected $_params = array();
	
	public function getHierarchy(){
		return $this->_hierarchy;
	}
	
	public function setHierarchy($hierarchy) {
		$this->_hierarchy = $hierarchy;
	}
	
	public function getParams() {
		return $this->_params;
	}
	
	public function addParams($key, $value) {
		$this->_params[$key] = $value;
	}
	
	public function getParam($key, $default = null){
		return (isset($this->_params[$key]))? $this->_params[$key] : $default;
	}
	
}