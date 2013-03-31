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
		
		$params = array();
		
		if($result){

			$this->_hierarchy = count(explode("/",$url)) - count($matchesLocal[1]);
			
			foreach($matchesLocal[1] as $key => $value){
				if(isset($matchesUrl[$key+1])){
					
					if(isset($this->_options["requirements"]) && isset($this->_options["requirements"][$value]))
						if(!preg_match("#{$this->_options["requirements"][$value]}#i", $matchesUrl[$key+1]))
							return false;
						
					$params[$value] = $matchesUrl[$key+1];
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
					
					$params[$var] = $args[$i];
				}
			}
			
			$this->_params = array_merge($this->_params, $params);
		}
		
		return $result==1;
	}
	
}