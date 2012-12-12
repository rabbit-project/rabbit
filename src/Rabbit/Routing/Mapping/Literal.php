<?php
namespace Rabbit\Routing\Mapping;

use Symfony\Component\HttpFoundation\Request;

class Literal extends MappingAbstract{
	
	public function match(Request $request) {
		$url = $request->getPathInfo();
		$urlPart = explode("/", trim(urldecode($url)));
		
		$regex = array();
		
		foreach(explode("/", $this->_url) as $pos => $part){
			if(strpos($part, ":")===0){
				$this->addParam(str_replace(":", "", $part), (isset($urlPart[$pos])? $urlPart[$pos] : ''));
				$regex[] = preg_replace("#:(.*)#", "(.*)", $part);				
			}else if(strpos($part, "*")===0) {
				$y = 0;
				for($i=$pos;$i<count($urlPart);$i++){
					$isVar = ++$y % 2;
					if($isVar === 1) {
						$var = $urlPart[$i];
						continue;
					}
					$this->addParam($var, $urlPart[$i]);
				}
			}else{
				$regex[] = $part;
				$this->_hierarchy +=1; //tipos comuns tem mais força então ganha pontos
			}
		}
		
		return preg_match('#^' . implode("/", $regex) . '/?$#i', $url);
	}
	
}