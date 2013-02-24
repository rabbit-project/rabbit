<?php
namespace Rabbit\Logger;

use Rabbit\Event\EventManager;

class LoggerManager {
	
	/**
	 * @var LoggerManager
	 */
	private static $_instance;
	private $_logger;
	private $_active = false;
	private $_trace = false;
	private $_exportConfig;
	
	/**
	 * @var LoggerType
	 */
	private $_nivelLogger;
	
	private function __construct() {
		$this->_nivelLogger = LoggerType::get("DEBUG");
		EventManager::registerListener("logger-register", array($this,"loggerPrinterFile"));
	}
	
	/**
	 * Singleton
	 * @return LoggerManager
	 */
	public static function getInstance() {
		if(!self::$_instance instanceof LoggerManager)
			self::$_instance = new self;
		return self::$_instance; 
	}
	
	/**
	 * @param String $name
	 * @return LoggerInterface
	 */
	public function getLogger($name) {
		if(isset($this->_logger[$name]))
			return $this->_logger[$name];
		
		$this->_logger[$name] = new SimpleLogger($name); 
		return $this->_logger[$name];
	}
	
	/**
	 * Grava logger no arquivo
	 * @param LoggerInterface $logger
	 */
	public function loggerPrinterFile(LoggerInterface $logger) {
		if(!$this->isActive())
			return;
		
		if($logger->getType()->getValue()>=$this->_nivelLogger->getValue()){
			$file = ($this->_exportConfig && isset($this->_exportConfig['file']))? $this->_exportConfig['file'] : RABBIT_PATH . '/temp/logger/rabbit.log';
			$maxSize = ($this->_exportConfig && isset($this->_exportConfig['maxSizeRotation']))? $this->_exportConfig['maxSizeRotation'] : '2MB';
			
			// Rotation File
			if(file_exists($file)){
				$size = ceil(filesize($file) / 1024);
				if($size>=$maxSize){
					$i = 1;
					while(true){
						$fRename = $file.$i++;
						if(!file_exists($fRename)){
							rename($file, $fRename);
							break;
						}
					}
				}
			}
			
			$resource = fopen($file, 'a');
			
			$logString = sprintf("[%s][%s][%s]: %s %s\r\n", 
				$logger->getType()->getName(), 
				date("d/m/Y H:i:s", $logger->getDateTime()), 
				$logger->getClassName(), 
				$logger,
				($this->isTrace() && $logger->getException())? sprintf("\nMessage: %s\nTrace:\n%s", $logger->getException()->getMessage(), $logger->getException()->getTraceAsString()) : ''
			);
			
			fwrite($resource, $logString);
			fclose($resource);
			
		}
	}
	
	public function setNivelLogger(LoggerType $nivel) {
		$this->_nivelLogger = $nivel;
	}
	
	public function setActive($active) {
		$this->_active = $active;
	}
	
	public function isActive() {
		return $this->_active;
	}
	
	public function setTrace($trace) {
		$this->_trace = $trace;
	}
	
	public function isTrace() {
		return $this->_trace;
	}
	
	public function setExportConfig($conf) {
		$this->_exportConfig = $conf;
	}
}