<?php
namespace Rabbit\View\Helper;

class HeadLink extends HelperAbstract{

	private $links = array();

	public function headLink($href=null, $type='', $rel='', $extra=array()) {
		if($href==null || empty($href))
			return $this;

		$link = array('href'=>$href,'type'=>$type,'rel'=>$rel,'extra'=>$extra);
		$this->addLink($href, $type, $rel, $extra);
		return $this;
	}

	public function append($href='', $type='', $rel = '', $extra=array()){
		$this->addLink($href, $type, $rel, $extra);
		return $this;
	}



	public function addLink($href='', $type='', $rel = '', $extra=array(), $addFirstPosition = false){

		# verifica se já existe para não inserir novamente
		foreach($this->links as $link){
			if($link['href'] == $href)
				return $this;
		}

		$link = array('href'=>$href,'type'=>$type,'rel'=>$rel,'extra'=>$extra);

		(!$addFirstPosition)? array_push($this->links, $link) : array_unshift($this->links, $link);

		return $this;
	}

	public function prepend($href='', $type='', $rel = '', $extra=array()){
		$this->addLink($href, $type, $rel, $extra, true);
		return $this;
	}

	public function __toString(){
		$linkOut = '';
		foreach($this->links as $link){
			$extra = '';
			if(!empty($link['extra']))
				foreach($link['extra'] as $key => $value)
					$extra .= sprintf('%s="%s" ',$key,$value);

			$linkOut .= sprintf('<link type="%s" rel="%s" href="%s" %s/>' . PHP_EOL, $link['type'], $link['rel'], $link['href'], $extra);
		}

		return $linkOut;
	}

}