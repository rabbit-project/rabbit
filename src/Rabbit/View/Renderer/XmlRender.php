<?php
namespace Rabbit\View\Renderer;

class XmlRender implements RenderInterface {
	
	private $_config;
	private $_simpleXml;
	
	public function __construct($config) {
		$this->_config = $config;
		if(isset($config["args"])){
			$this->_simpleXml = new \SimpleXMLElement("<root />");
			$this->parserDataForXml($config["args"]);
		}
	}
	public function render() {
		return $this->_simpleXml->asXML();
	}
	
	private function parserDataForXml($data, $xml = null, $keyT=null) {
		$xml = ($xml!==null)? $xml : $this->_simpleXml;
			
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