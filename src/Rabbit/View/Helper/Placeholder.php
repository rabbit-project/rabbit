<?php
namespace Rabbit\View\Helper;

class Placeholder extends HelperAbstract{

	protected $contents = array();
	protected $name;

	public function placeholder($name) {
		$this->name = $name;
		return $this;
	}

	public function __toString(){
		return isset($this->contents[$this->name])? $this->contents[$this->name] : '';
	}

	public function captureStart() {
		ob_start();
	}

	public function captureEnd() {
		$this->contents[$this->name] = ob_get_clean();
	}

	public function set($content){
		$this->contents[$this->name] = $content;
	}

	public function get($name) {
		return isset($this->contents[$name])? $this->contents[$name] : '';
	}

}