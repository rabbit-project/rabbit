<?php
namespace Rabbit\Annotation;

class Annotation {
	
	/**
	 * @var \ReflectionClass
	 */
	private $_rc;
	
	public function __construct($cls) {
		$this->_rc = new \ReflectionClass($cls);
	}
	
	public function getAnnotationsMethodName($name) {
		$comment = $this->_rc->getMethod($name)->getDocComment();
		return $this->getAnnotationComment($comment);
	}
	
	public function getAnnotationClass() {
		
	}
	
	private function getAnnotationComment($comment){
		echo $comment;
	}
}