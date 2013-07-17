<?php
namespace Rabbit\Controller;

use Rabbit\Application\Front;
use Rabbit\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class Action{

	public static function forward(array $args) {
		return View::getHelper('action', array($args));
	}

	public static function render($args = NULL, array $config = array()) {
		$request = Front::getInstance()->getRequest();

		$module 		= isset($config['module'])?		$config['module'] 		: $request->get("module");
		$namespace 		= isset($config['namespace'])? 	$config['namespace'] 	: $request->get("namespace");
		$controller 	= isset($config['controller'])? 	$config['controller'] 	: $request->get("controller");
		$action 		= isset($config['action'])?		$config['action'] 		: $request->get("action");

		$prefix = isset($config['prefix'])?	$config['prefix'] : 'phtml';

		$fileURI = RABBIT_PATH_MODULE . DS . ucfirst($module) . DS . 'view' . DS .  $namespace . DS . $controller . DS . $action . '.' .$prefix;

		$config['uri-view'] = $fileURI;

		return new View($args, $config);
	}

	public static function redirectTo($url, $status = 302, array $headers = array() ) {
		return RedirectResponse::create($url, $status, $headers)->send();
	}
}