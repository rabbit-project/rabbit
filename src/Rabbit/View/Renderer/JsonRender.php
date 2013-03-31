<?php
namespace Rabbit\View\Renderer;

use Rabbit\View\Exception\ViewException;

class JsonRender implements RenderInterface {

	private $_config;

	public function __construct($config) {
		$this->_config = $config;
	}

	public function render() {
		if(isset($this->_config['args']))
			return json_encode($this->_config['args']);
	}
	
}