<?php
namespace Rabbit\Layout;

use Composer\DependencyResolver\Request;
use Rabbit\Layout\Excepiton\LayoutException;
use Rabbit\View\View;
use Symfony\Component\HttpFoundation\Response;

class Layout {

	private $_layout;
	private $_args;
	private $_argsReserved = array('layout','content');

	public function render($content) {
		$this->_args = array('content' => $content);
		$config = array('uri-view' => $this->_layout);

		$viewRender = new View($this->_args, $config);

		if(!defined('RABBIT_LAYOUT_HTML_COMMON_URI'))
			throw new LayoutException('Não foi possível a configuração <strong>RABBIT_LAYOUT_HTML_COMMON_URI</strong>');

		# Carregando HTML commum
		$args = array('layout' => $viewRender->render());
		$config = array('uri-view'=> RABBIT_LAYOUT_HTML_COMMON_URI);

		$viewRender = new View($args, $config);

		return $viewRender->render();

	}

	public function __set($name,$value) {
		if(in_array($name,$this->_argsReserved))
			throw new LayoutException(sprintf('A propriedade <strong>%s</strong> é uma propriedade reservada o mesmo não pode ser modificado', $name));
		$this->_args[$name] = $value;
	}

	/**
	 * @param mixed $layout
	 */
	public function setLayout($layout) {
		$this->_layout = RABBIT_LAYOUT_THEMES_PATH . DS . $layout . DS . 'layout.phtml';
	}

	/**
	 * @return mixed
	 */
	public function getLayout() {
		return $this->_layout;
	}

}