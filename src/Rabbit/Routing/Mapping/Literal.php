<?php
namespace Rabbit\Routing\Mapping;

use Symfony\Component\HttpFoundation\Request;

class Literal extends MappingAbstract{
	
	public function match(Request $request) {
		
		$url = trim(urldecode($request->getPathInfo()));
		
		if(!$request->getBasePath())
			$url = preg_replace("#^/|" . implode("|",explode("/", $request->getScriptName())) . "#", "", $url);
		
		$urlPart = explode("/", $url);
		$urlMapPart = explode("/", $this->_url);
		
		$regex = array();
				
		foreach($urlMapPart as $pos => $part){
			if(strpos($part, ":")===0){
				$uPart = isset($urlPart[$pos])? $urlPart[$pos] : '';
				$paramName = str_replace(":", "", $part);
				
				if(isset($this->_requirements[$paramName]) && !preg_match("#" . $this->_requirements[$paramName] . "#",$uPart))
					return false;					
				
				$this->addParam($paramName, $uPart);
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
		print_pre('#^' . implode("/", $regex) . '/?$#i');
		print_pre($url . ' - ' .  $this->_url);
		var_dump(preg_match('#^' . implode("/", $regex) . '/?$#i', $url));
		print_pre($request->getBasePath());
		preg_match("#\[(.*)\]#", $this->_url, $ar);
		print_pre($ar);
		return preg_match('#^' . implode("/", $regex) . '/?$#i', $url);
	}
	
}