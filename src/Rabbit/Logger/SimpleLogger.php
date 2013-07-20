<?php
namespace Rabbit\Logger;

use Rabbit\Event\EventManager;

class SimpleLogger implements LoggerInterface {
	
	private $_message;
	private $_type;
	private $_exception;
	private $_timestamp;
	private $_clsName;
	
	public function __construct($clsName){
		$this->_clsName = $clsName;
	}
	
	public function log($message, LoggerType $type, \Exception $e = null){
		$this->_message = $message;
		$this->_type = $type;
		$this->_exception = $e;
		$this->_timestamp = time();
		EventManager::fire('Rabbit\Event\Log\Register', array($this));
	}
	
	public function __toString() {
		return sprintf("%s", $this->_message);
	}
	
	public function getType() {
		return $this->_type;
	}
	
	public function getDateTime() {
		return $this->_timestamp;
	}
	
	public function getMessage() {
		return $this->_message;
	}
	
	public function getClassName() {
		return $this->_clsName;
	}
	
	public function getException(){
		return $this->_exception;
	}
}