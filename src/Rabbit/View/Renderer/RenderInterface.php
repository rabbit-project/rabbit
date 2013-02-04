<?php
namespace Rabbit\View\Renderer;

interface RenderInterface {
	
	public function __construct($data);
	
	public function render();
	
}