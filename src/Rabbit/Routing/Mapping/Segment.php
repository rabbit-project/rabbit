<?php
namespace Rabbit\Routing\Mapping;

use Symfony\Component\HttpFoundation\Request;

/**
 * Segment
 * 
 * Faz a combinação de uma determinada URL baseado em segmento
 * 
 * @author Erick Leao <erickleao@rabbitcms.com.br>
 */
class Segment extends RouterMappingAbstract{
	
	private $_url;
	private $_options;
	private $_scapeRegexs = array("\.","\-","\*");
	
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
		
		$urlPart = explode("/", $url);
		$urlPart = explode("/", $this->_url);
		
		// Pega todos os parametros
		preg_match_all("#:([[:alnum:]\w]+)#i", $this->_url, $matchesLocal);
		
		// monta a expressão regular para fazer o match na URL
		$regex = str_replace(
			"\*",
			"(?<args>.*)",
			preg_replace(
				"#\\\:([[:alnum:]\w]+)?#", "([^/]+)", 
				str_replace(array("\[","\]"), array("(?:",")?"),
					 preg_quote($this->_url)
				)
			)
		);
		
		$result = preg_match("#{$regex}#", $url, $matchesUrl);
		
		if($result){
			
			$this->_hierarchy = strlen($matchesUrl[0]);
			unset($matchesUrl[0]);
			
			foreach($matchesUrl as $key => $value){
				if(is_string($key))
					continue;
				
				if(isset($matchesLocal[1][$key-1])){
					
					if(isset($this->_options["requirements"]) && isset($this->_options["requirements"][$matchesLocal[1][$key-1]])){
						if(!preg_match("#{$this->_options["requirements"][$matchesLocal[1][$key-1]]}#", $value)){
							return false;
						}
					}
						
					$matchesUrl[$matchesLocal[1][$key-1]] = $value;
				}
				
				
				unset($matchesUrl[$key]);
			}
			
			// Recupera os argumetos do wildcard *
			if(isset($matchesUrl["args"])){
				$args = explode("/", $matchesUrl["args"]);
				for($i=0,$t=count($args);$t--;$i++) {
					if($i%2==0){
						$var = $args[$i];
						continue;
					}
					
					$matchesUrl[$var] = $args[$i];
				}
			}
			
			$this->_params = array_merge($this->_params, $matchesUrl);
		}
		
		return $result;
	}
	
}