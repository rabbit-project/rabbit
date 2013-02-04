<?php
namespace Rabbit\View\Renderer;

class JsonRender implements RenderInterface {
	
	private $data;
	
	public function __construct($data) {
		$this->data = $data;
	}
	
	public function render() {
		return json_encode($this->data);
	}
	
}