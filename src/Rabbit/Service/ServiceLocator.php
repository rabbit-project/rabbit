<?php
namespace Rabbit\Service;

use Rabbit\Service\Exception\ServiceException;

abstract class ServiceLocator {
	
	private static $_service = array();
	private static $_instance = array();
	
	private function __construct() {}
	private function __clone() {}
	
	public static function getService($name, array $params=array()) {
		if(isset(self::$_instance[$name]))
			return self::$_instance[$name];
		
		$service = isset(self::$_service[$name])? self::$_service[$name] : array("type"=>$name,"singleton"=>false);
		
		if($service["type"] instanceof \Closure){
			$object = call_user_func_array($service["type"], $params);
		}else{
			$object = self::reflexionInstance($service["type"]); 
		}
		
		if($service["singleton"]==true)
			self::$_instance[$name] = $object;
		
		return $object;
	}
	
	public static function register($name, $type, $singleton = null ) {
		self::$_service[$name]["type"] = $type;
		self::$_service[$name]["singleton"] = $singleton;
	}
	
	public static function isRegistred($name) {
		return array_key_exists($name, self::$_service) || array_key_exists($name, self::$_instance);
	}

	protected static function reflexionInstance($cls) {
		$rc = new \ReflectionClass($cls);		
		
		if(!$rc->isInstantiable())
			throw new ServiceException(sprintf("Não é possível instanciar uma interface/abstract: <strong>%s</strong>", $cls));
				
		$construct = $rc->getConstructor();
		
		if(is_null($construct))
			return $rc->newInstanceWithoutConstructor();
		
		$args = array();
		
		foreach($construct->getParameters() as $param) {
			
			if($param->isDefaultValueAvailable())
				break;
			
			$clsParam = $param->getClass();
			
			if(is_null($clsParam))
				throw new ServiceException(sprintf("O paramentro <strong>%s</strong> possue uma dependência e o mesmo não pode ser localizado para: <strong>%s</strong>", $param->getName(), $cls));
			
			$args[] = self::reflexionInstance($clsParam->getName());
		}
		
		return $rc->newInstanceArgs($args);
		
	}
	
	public static function registerInstance($name, $instance) {
		self::$_instance[$name] = $instance;
	}
}