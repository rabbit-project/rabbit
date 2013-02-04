<?php
namespace Rabbit\View\Renderer;

class XmlRender implements RenderInterface {
	
	private $data;
	private $_simpleXml;
	
	public function __construct($data) {
		$this->data = $data;
		$this->_simpleXml = new \SimpleXMLElement("<root />");
		$this->parserDataForXml($data["params"]);
	}
	public function render() {
		return $this->_simpleXml->asXML();
	}
	
	private function parserDataForXml($data, $xml = null, $keyT=null) {
		$xml = ($xml!==null)? $xml : $this->_simpleXml;
		
		/*if($keyT!==null)
			$xml = $xml->addChild($keyT);*/
			
		foreach($data as $key => $value){
			if(is_array($value)){
				if(!is_numeric($key)){
					$this->parserDataForXml($value, $xml->addChild($key . 's'), $key);
				}else{
					$this->parserDataForXml($value, $xml);
				}
			}else{
				$xml->addChild($key, $value);
			}
		}
	}
	
}