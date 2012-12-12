<?php

namespace Rabbit\View;

use Symfony\Component\HttpFoundation\Request;

use Rabbit\View\ViewFileNotFoundException;
use Rabbit\View\ViewInterface;

class View implements ViewInterface{
	
	protected $request;
	
	private $_acceptDefault = "html";
	
	private $datas;
	private $_config = array();
	
	public function __construct($datas = null, array $config = array()){
		$this->datas = $datas;
		if(is_array($datas) || is_object($datas))
			$this->registerDatasThisClass($datas);
		$this->_config = $config;
	}
	
	public function registerDatasThisClass($datas) {
		foreach($datas as $key => $value)
			$this->$key = $value;
	}
	
	public function setRequest(Request $request) {
		$this->request = $request;
	}
	
	/**
	 * @return Request
	 */
	public function getRequest() {
		return $this->request;
	}
	
	public function render($fileURI) {
		
		if(!file_exists($fileURI))
			throw new ViewFileNotFoundException(sprintf("Não foi possível localizar o arquivo <strong>%s</strong>", $fileURI));
		
		ob_start();
		include $fileURI;
		return ob_get_clean();
	}
	
}