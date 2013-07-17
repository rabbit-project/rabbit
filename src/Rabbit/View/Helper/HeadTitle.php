<?php
namespace Rabbit\View\Helper;

class HeadTitle extends HelperAbstract{

	private $title = array();
	private $separator = ' ';

	public function headTitle($title=null) {
		if($title == null || empty($title))
			return $this;

		$this->title[] = $title;
		return $this;
	}

	public function setSeparator($separator){
		$this->separator = $separator;
		return $this;
	}

	public function append($title){
		array_push($this->title, $title);
		return $this;
	}

	public function prepend($title){
		array_unshift($this->title, $title);
		return $this;
	}

	public function __toString(){
		return sprintf('<title>%s</title>' . PHP_EOL, rtrim(implode($this->separator, $this->title),$this->separator));
	}

}