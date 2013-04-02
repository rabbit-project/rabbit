<?php
namespace Rabbit\Routing\Mapping;

use Symfony\Component\HttpFoundation\Request;

/**
 * RegexMap
 * 
 * Faz a combinação de uma determinada URL baseado em expressão regular
 * 
 * @author Erick Leao <erickleao@rabbitcms.com.br>
 */
class Regex extends RouterMappingAbstract{
	
	private $_regex;
	private $_options = array();
	
	public function __construct($regex, array $defautls = array(), array $options = array()){
		$this->_regex = $regex;
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
		
		$result = preg_match('#' . str_replace('#', '\#', $this->_regex) . '#', $url, $matches);
		
		if($result){
			
			$this->_hierarchy = strlen($matches[0]);
			unset($matches[0]);
			
			foreach($matches as $key => $value){
				if(isset($this->_options["matches_ref"]) && ($ref = array_search($key, $this->_options["matches_ref"])))
					$matches[$ref] = $value;
					
				if(is_int($key))
					unset($matches[$key]);
			}
			
			$this->_params = array_merge($this->_params, $matches);
		}

		return $result;
	}
	
}