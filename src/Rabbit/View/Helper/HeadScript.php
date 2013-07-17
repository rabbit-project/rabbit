<?php
namespace Rabbit\View\Helper;

class HeadScript extends HelperAbstract{

	private $scripts = array();
	private $files = array();

	public function headScript() {
		return $this;
	}

	public function appendScript($script, $type='text/javascript', $extra = array()) {
		$this->addScript($script, $type, $extra);
		return $this;
	}

	public function prependScript($script, $type='text/javascript', $extra = array()){
		$this->addScript($script, $type, $extra, true);
		return $this;
	}

	private function addScript($script, $type='text/javascript', $extra = array(), $addFirstPosition = false) {

		$scriptIn = array(
			'content' => $script,
			'type' => $type,
			'extra' => $extra
		);

		(!$addFirstPosition)? array_push($this->scripts,$scriptIn) : array_unshift($this->scripts,$scriptIn);

	}

	public function addFile($src, $type='text/javascript', $extra = array(), $addFirstPosition = false){

		# verifica se o link já foi inserido
		foreach($this->files as $file)
			if($file['src'] == $src)
				return $this;

		$src = array(
			'src' => $src,
			'type' => $type,
			'extra' => $extra
		);

		(!$addFirstPosition)? array_push($this->files,$src) : array_unshift($this->files,$src);
	}

	public function appendFile($src, $type='text/javascript', $extra = array()) {
		$this->addFile($src, $type, $extra);
		return $this;
	}

	public function prependFile($src, $type='text/javascript', $extra = array()){
		$this->addFile($src, $type, $extra, true);
		return $this;
	}

	public function captureScriptStart($name,$type='text/javascript', $extra = array()) {
		$this->scripts[$name]['type'] = $type;
		$this->scripts[$name]['extra'] = $extra;
		ob_start();
	}

	public function captureScriptEnd($name) {
		if(!isset($this->scripts[$name]))
			return NULL;

		$this->scripts[$name]['content'] = ob_get_clean();
	}

	public function __toString(){
		$scriptOut = '';

		# montando os filesScripts
		foreach($this->files as $file){
			$extra = '';
			if(!empty($file['extra']))
				foreach($file['extra'] as $key => $value)
					$extra .= sprintf('%s="%s" ',$key,$value);

			$scriptOut .= sprintf('<script src="%s" type="%s" %s></script>' . PHP_EOL, $file['src'], $file['type'], $extra);
			
		}

		# montando os scripts
		foreach($this->scripts as $script){
			$extra = '';
			if(!empty($script['extra']))
				foreach($script['extra'] as $key => $value)
					$extra .= sprintf('%s="%s" ',$key,$value);

			$scriptOut .= sprintf('<script type="%s" %s>%s</script>' . PHP_EOL, $script['type'], $extra, $script['content']);

		}

		return $scriptOut;
	}

}