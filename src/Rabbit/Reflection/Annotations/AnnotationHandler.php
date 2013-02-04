<?php
namespace Rabbit\Reflection\Annotations;

class AnnotationHandler {
	
	/**
	 * @var multitype:\Rabbit\Annotations\Annotation
	 */
	private $_annotations = array();
	
	/**
	 * @param string $name
	 * @return NULL|\Rabbit\Annotations\Annotation
	 */
	public function get($name) {
		return (isset($this->_annotations[$name]))? $this->_annotations[$name] : null; 
	}
	
	/**
	 * 
	 * @return multitype:\Rabbit\Annotations\Annotation
	 */
	public function all() {
		return $this->_annotations;
	}
	
	/**
	 * @param string $name
	 * @return boolean
	 */
	public function exists($name) {
		return array_key_exists($name, $this->_annotations);
	}
	
	/**
	 * @param string $name
	 * @param \Rabbit\Annotations\Annotation $ann
	 * @return \Rabbit\Annotations\AnnotationHandler
	 */
	public function add($name, Annotation $ann) {
		$this->_annotations[$name] = $ann;
		return $this;
	}
	
	
}