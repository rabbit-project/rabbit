<?php
namespace Rabbit\Reflection\Annotations;

abstract class Annotation {
	
	protected $_value = '';
	
	public function set($key, $value){
		if(!isset($this->$key))
			throw new \Exception("A propriedade {$key} não é um mapeamento da annotation " . get_class($this));
	}
	
	public function get($key){
		return isset($this->_properties[$key])? $this->_properties[$key]: null;
	}
	
	public function getValue(){
		return $this->_value;
	}
	
	public function setValue($value) {
		$this->_value = $value;
	}
	
	public function getProperties(){
		return $this->_properties;
	}
} 