<?php
namespace Rabbit\Reflection;

use Rabbit\Reflection\Annotations\AnnotationBuild;

class ClassReflection extends \ReflectionClass {
		
	public function annotation() {
		return new AnnotationBuild($this);
	}
	
}