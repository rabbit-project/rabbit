<?php
namespace Rabbit\Reflection\Annotations;

use Rabbit\Reflection\Annotations\CommentParser;

use Rabbit\Reflection\ClassReflection;

class AnnotationBuild {
	
	private $_cls;
	private $_annotations = array();
	
	public function __construct(ClassReflection $cls) {
		$this->_cls = $cls;
		$this->execute();
	}
	
	private function execute() {
		$methods = $this->_cls->getMethods();
		foreach($methods as $method){
			
			$docParser = new CommentParser($method->getDocComment());
			if($docParser->hasAnnotation())
				$this->_annotations['method'][$method->getName()] = $docParser->getAnnotations();
				
		}
		
	}
	
	/**
	 * @param string $name
	 * @return NULL|AnnotationHandler
	 */
	public function atMethod($name) {
		return isset($this->_annotations['method'][$name])? $this->_annotations['method'][$name] : null;
	}
	
	public function atField($name) {
		
	}
	
	public function atClass() {
		
	}
}