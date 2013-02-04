<?php
namespace Rabbit\View;

use Rabbit\View\Renderer;

class RenderFactory {
	
	/**
	 * @param string $type
	 * @return Renderer\RenderInterface
	 */
	public static function getRenderer($type, $data) {
		$nameCls = "Rabbit\View\Renderer\\" . ucfirst($type) . "Render";
		return new $nameCls($data);
	}
	
}