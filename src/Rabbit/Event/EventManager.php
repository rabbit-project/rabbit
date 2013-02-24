<?php

namespace Rabbit\Event;

abstract class EventManager {
	
	private static $_instance;
	
	private static $_events = array();
	
	private function __clone(){}
	private function __construct(){}
	
	public static function fire($eventName, array $obj=array(), &$returns=array()) {
		if(isset(self::$_events[$eventName]))
			foreach(self::$_events[$eventName] as $event)
				$returns[] = call_user_func_array($event, $obj);
	}
	
	public static function registerListener($eventName, callable $fn) {
		self::$_events[$eventName][] = $fn;
	}
	
}