<?php
namespace Rabbit\Service;

use Rabbit\ServiceException\ServiceException;
use \Closure;

class ServiceLocator {
	
	private static $_service = array();
	
	public static function getService($name) {
		if(isset(self::$_service[$name])){
			$f = self::$_service[$name]
			return call_user_func_array($f, self::$_service[$name]["args"]);
		}
		
		throw new ServiceException(sprintf("Não foi possível localizar o serviço <strong>%s</strong>",$name));
	}
	
	public static function register($name, Closure $call) {
		self::$_service[$name] = $call;
	}
	
	public static function isRegistred($name) {
		return array_key_exists($name, self::$_service[$name]);
	}

	protected static function reflexion() {
		
		self::getService($name);
	}
}