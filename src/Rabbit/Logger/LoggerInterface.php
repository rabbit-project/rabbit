<?php
namespace Rabbit\Logger;

interface LoggerInterface {
	
	public function log($message, LoggerType $type, \Exception $e=null);
	
	/**
	 * @return LoggerType
	 */
	public function getType();
	
	public function getMessage();
	
	public function getDateTime();
	
	public function getClassName();
	
	/**
	 * @return \Exception
	 */
	public function getException();
	
	public function __construct($clsName);
	
}