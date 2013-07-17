<?php
namespace Rabbit\View\Helper;

class HeadStyle extends HelperAbstract{

	private $styles = array();

	public function headStyle() {
		return $this;
	}

	public function appendStyle($style, $type='text/css', $extra = array()) {
		$this->addStyle($style, $type, $extra);
		return $this;
	}

	public function prependStyle($style, $type='text/css', $extra = array()){
		$this->addStyle($style, $type, $extra, true);
		return $this;
	}

	private function addStyle($style, $type='text/css', $extra = array(), $addFirstPosition = false) {

		$styleIn = array(
			'content' => $style,
			'type' => $type,
			'extra' => $extra
		);

		(!$addFirstPosition)? array_push($this->styles,$styleIn) : array_unshift($this->styles,$styleIn);

	}


	public function captureStyleStart($name, $type='text/css', $extra = array()) {
		$this->styles[$name]['type'] = $type;
		$this->styles[$name]['extra'] = $extra;
		ob_start();
	}

	public function captureStyleEnd($name) {
		if(!isset($this->styles[$name]))
			return NULL;

		$this->styles[$name]['content'] = ob_get_clean();
	}

	public function __toString(){
		$styleOut = '';

		# montando os style
		foreach($this->styles as $style){
			$extra = '';
			if(!empty($style['extra']))
				foreach($style['extra'] as $key => $value)
					$extra .= sprintf('%s="%s" ',$key,$value);

			$styleOut .= sprintf('<style type="%s" %s>%s</style>' . PHP_EOL, $style['type'], $extra, $style['content']);

		}

		return $styleOut;
	}

}