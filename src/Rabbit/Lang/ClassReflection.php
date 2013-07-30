<?php
namespace Rabbit\Lang;

/**
 * Class ClassReflection
 * Trait para representar a reflexão da classe
 * ex:
 *
 * class MyClass {
 *    use ClassReflection
 * }
 *
 * MyClass::getCLass()->getInstance()
 *
 * @package Rabbit\Lang
 */
trait ClassReflection {

	/**
	 * @return \ReflectionClass
	 */
	public static function getClass(){
		return new \ReflectionClass(get_class());
	}

}