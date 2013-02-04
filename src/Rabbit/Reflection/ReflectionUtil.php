<?php
namespace Rabbit\Reflection;

class ReflectionUtil {
	
	public static function on($cls) {
		return new ClassReflection($cls);
	}
	
}