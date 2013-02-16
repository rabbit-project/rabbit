<?php
namespace Rabbit\Routing\Mapping;

use Symfony\Component\HttpFoundation\Request;

/**
 * RegexMap
 * 
 * Faz a combinação de uma determinada URL baseado em um mapeamento
 * 
 * @author Erick Leao <erickleao@rabbitcms.com.br>
 */
class RegexMap extends MappingAbstract{
	
	/**
	 * Combina uma URL com o padrão mapeado
	 * 
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return boolean
	 */
	public function match(Request $request) {
		
		$url = trim(urldecode($request->getPathInfo()));
		
		if(!$request->getBasePath())
			$url = preg_replace("#^/|" . implode("|",explode("/", $request->getScriptName())) . "#", "", $url);
		
		$urlPart = explode("/", $url);
		$urlMapPart = explode("/", $this->_url);
		
		$regex = array();
				
		foreach($urlMapPart as $pos => $part){
			
			if(preg_match("#:#",$part)){
				$uPart = isset($urlPart[$pos])? $urlPart[$pos] : '';				
				$rex = preg_replace("#:(.[a-zA-Z0-9]+)#", "(.*)", preg_replace("#(\.)#","\\\\\\1",  $part));
				
				if(preg_match("#" . str_replace(array("[","]"), "", $rex) . "#", $uPart, $rexArgs)){
					preg_match_all("#:(.[a-zA-Z0-9]+)?#", $part, $rexVars);
					foreach ($rexVars[1] as $key => $varsName){
						
						if(isset($this->_requirements[$varsName]) && !preg_match("#" . $this->_requirements[$varsName] . "#",$rexArgs[$key+1]))
							return false;
						
						$this->addParam($varsName, $rexArgs[$key+1]);
					}
				}
				
				$regex[] = "." . $rex;
			
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
				
				if(preg_match("#]#", $part))
					$regex[] = str_replace("]", ")?", str_replace("*", "(.*)", $part));
			}else{
				$regex[] = preg_replace("#(\.)#","\\\\\\1",$part);
				$this->_hierarchy +=1; //tipos comuns tem mais força então ganha pontos
			}
		}
		
		/* var_dump(preg_match('#^' . str_replace("]", ")?",str_replace("[", "(",implode("/", $regex))) . '/?$#i', $url));
		print_pre('#^' . str_replace("]", ")?",str_replace("[", "(",implode("/", $regex))) . '/?$#i');		
		print_pre($url . ' - ' .  $this->_url);
		print_pre($request->getBasePath());
		preg_match("#\[(.*)\]#", $this->_url, $ar);
		print_pre($ar);
		print_pre($this->getParams());
		print_pre($this->getHierarchy());
		echo "-----";*/
		
		return preg_match('#^' . str_replace("]", ")?",str_replace("[", "(",implode("/", $regex))) . '/?$#i', $url);
	}
	
}