<?php
namespace Rabbit\Logger;

use Rabbit\Event\EventManager;
use Rabbit\Logger\Exception\LoggerException;

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
		EventManager::registerListener('Rabbit\Event\Log\Register', array($this,"loggerPrinterFile"));
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
			$filePath = ($this->_exportConfig && isset($this->_exportConfig['filePath']))? $this->_exportConfig['filePath'] : null;

			if($filePath == null)
				throw new LoggerException(sprintf('Não foi mapeado o <strong>filePath</strong> para o export LoggerManager'));

			if(!file_exists($filePath))
				mkdir($filePath, 0755, true);

			$fileName = ($this->_exportConfig && isset($this->_exportConfig['fileName']))? $this->_exportConfig['fileName'] : 'rabbit.log';
			$maxSize = ($this->_exportConfig && isset($this->_exportConfig['maxSizeRotation']))? $this->_exportConfig['maxSizeRotation'] : '2MB';

			$fileURI = $filePath . DS . $fileName;

			// Rotation File
			if(file_exists($fileURI)){
				$size = ceil(filesize($fileURI) / 1024);
				if($size>=$maxSize){
					$i = 1;
					while(true){
						$fRename = $fileURI.$i++;
						if(!file_exists($fRename)){
							rename($fileURI, $fRename);
							break;
						}
					}
				}
			}
			
			$resource = fopen($fileURI, 'a');
			
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