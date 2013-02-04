<?php
namespace Rabbit\Reflection\Annotations;

use RabbitTest\Annotation\MyAnnotation;

use Rabbit\Reflection\Annotations\AnnotationHandler;

/**
 * @author Erick Leão
 */
class CommentParser {
	
	private $_comment;
	
	/**
	 * @var AnnotationHandler
	 */
	private $_annotationH = array();
	
	/**
	 * Annotations reservadas
	 * @var array
	 */
	private $_reserved = array('abstract','access','author','category','copyright'
			,'deprecated','example','final','filesource','global','ignore','internal'
			,'license','link','method','name','package','param','property','return'
			,'see','since','static','staticvar','subpackage','todo','tutorial'
			,'uses','var','version');
	
	public function __construct($comment) {
		$this->_comment = $comment;
		$this->execute();
	}
	
	private function execute() {
		
		preg_match_all("#@[a-zA-Z].*(\(.*?\))?#", $this->_comment, $arg);
		
		$handler = new AnnotationHandler();
		
		foreach($arg[0] as $ar){
			
			$annName = preg_replace("#@(.*)(\(.*\)).*#", "\\1", $ar);
			
			if(in_array($ar, $this->_reserved))
				continue;
			
			//$annotation = new namespaces\$clsName();
			//$annotation->set("name", "joao");
			$handler->add('@'.$annName, $annotation);
		}
		
		$this->_annotationH = $handler;
	}
	
	public function searchProperties($e) {
		
	}
	
	public function hasAnnotation() {
		return !empty($this->_annotationH);
	}
	
	/**
	 * @return multitype:AnnotationHandler
	 */
	public function getAnnotations() {
		return $this->_annotationH;
	}
	
}