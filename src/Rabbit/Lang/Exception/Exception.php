<?php
namespace Rabbit\Lang\Exception;

use Rabbit\Event\EventManager;

class ExceptionRabbit extends \Exception {

	public function __construct($message = "", $code = 0, Exception $previous = null){
		EventManager::fire('Rabbit\Event\Exception', array($this));
		parent::__construct($message, $code, $previous);
	}

}