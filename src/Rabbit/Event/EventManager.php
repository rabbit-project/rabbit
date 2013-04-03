<?php

namespace Rabbit\Event;

use Rabbit\Event\Exception\EventException;

abstract class EventManager {
	
	private static $_events = array();
	
	private function __clone(){}
	private function __construct(){}
	
	public static function fire($eventName, array $obj=array(), &$returns=array()) {
		if(isset(self::$_events[$eventName]))
			foreach(self::$_events[$eventName] as $event)
				$returns[] = call_user_func_array($event, $obj);
	}
	
	public static function registerListener($eventName, $fn) {
		if(!is_callable($fn))
			throw new EventException('O argumento 2 passado pelo EventManager::registerListener deve ser um tipo Callable');
		self::$_events[$eventName][] = $fn;
	}
	
}