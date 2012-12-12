<?php

namespace Rabbit\Routing\Mapping;

use Symfony\Component\HttpFoundation\Request;

abstract class MappingAbstract {
	
	protected $_controller;
	protected $_module;
	protected $_action;
	protected $_namespace;
	
	protected $_params;
	protected $_url;
	protected $_hierarchy;
		
	public function __construct($url, array $params = array()){
		$this->_url = $url;
		$this->_hierarchy = count(explode("/", $url)) - 1;
		$this->_params = $params;
		$this->init();
	}
		
	public function init() {
		//...
	}
	
	/**
	 * Verificar se a URL combina com o mapemento
	 * @param Request $request
	 */
	abstract public function match(Request $request);
	
	/**
	 * Recupera o controller
	 * @return string
	 */
	public function getController(){
		return $this->_controller;
	}
	
	public function setController($name) {
		$this->_controller = $name;
	}
	
	/**
	 * Recupera o module
	 * @return string
	 */
	public function getModule(){
		return $this->_module;
	}
	
	public function setModule($name) {
		$this->_module = $name;
	}
	
	/**
	 * Recupera a ação
	 * @return string
	 */
	public function getAction(){
		return $this->_action;
	}
	
	public function setAction($name){
		$this->_action = $name;
	}
	
	/**
	 * Recupera o namespace
	 * @return string
	 */
	public function getNamespace(){
		return $this->_namespace;
	}
	
	public function setNamespace($name) {
		$this->_namespace = $name;
	}
	
	public function getUrl() {
		return $this->_url;
	}
	
	public function getParams() {
		return $this->_params;
	}
	
	public function addParam($key, $value) {
		$this->_params[$key] = $value;
	}
	
	public function getHierarchy() {
		return $this->_hierarchy;
	}
}