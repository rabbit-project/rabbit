<?php
namespace Rabbit\View;

use Symfony\Component\HttpFoundation\Request;

interface ViewInterface {
	
	public function __construct($datas = null, array $config = array());
	
	/**
	 * Renderiza uma view
	 * @return string
	 */
	public function render($fileURI);
	
	public function setRequest(Request $request);
	
	/**
	 * @return Request
	 */
	public function getRequest();
	
}