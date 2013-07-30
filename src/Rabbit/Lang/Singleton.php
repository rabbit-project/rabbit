<?php
namespace Rabbit\Lang;


trait Singleton {

	/**
	 * @var $this
	 */
	private static $instance;

	protected function __construct(){ }
	protected function __clone() { }

	/**
	 * @return $this
	 */
	public static function getInstance(){
		if(!self::$instance instanceof Translate)
			self::$instance = new self;
		return self::$instance;
	}

}