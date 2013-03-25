<?php
namespace Rabbit\Routing\Mapping;

use Symfony\Component\HttpFoundation\Request;

/**
 * Literal
 * 
 * Faz a combinação de uma determinada URL que sejá igual ao informado no mapeamento
 * 
 * @author Erick Leao <erickleao@rabbitcms.com.br>
 */
class Literal extends RouterMappingAbstract{
	
	private $_url;
	private $_options;
	
	public function __construct($url, array $defautls = array(), array $options = array()){
		$this->_url = $url;
		$this->_options = $options;
		$this->_params = array_merge($this->_params, $defautls);
	}
	
	/**
	 * Combina uma URL com o padrão mapeado
	 * 
	 * @param Request $request
	 * @return boolean
	 */
	public function match(Request $request) {
		
		$url = trim(urldecode($request->getPathInfo()));
		
		if(!$request->getBasePath())
			$url = preg_replace("#^/|" . implode("|",explode("/", $request->getScriptName())) . "#", "", $url);
		
		return $this->_url == $url;
	}
	
}