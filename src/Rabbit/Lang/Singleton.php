<?php
namespace Rabbit\Lang;


trait Singleton {

	/**
	 * @var Translate
	 */
	private static $instance;

	protected function __construct(){ }
	protected function __clone() { }

	public static function getInstance(){
		if(!self::$instance instanceof Translate)
			self::$instance = new self;
		return self::$instance;
	}

}