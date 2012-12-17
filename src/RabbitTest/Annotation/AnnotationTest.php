<?php
namespace RabbitTest\Annotation;

use Rabbit\Annotation\Annotation;

class AnnotationTest extends \PHPUnit_Framework_TestCase {
	
	
	public function testApplication() {
		Annotation::getAnnotation($this->teste);
	}
	
	/**
	 * @Annotation("")
	 */
	private function teste() {
		
	}
	
	
}