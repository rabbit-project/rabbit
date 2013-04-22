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
		return new View($args, $config);
	}

	public static function redirectTo($url, $status = 302, array $headers = array() ) {
		return RedirectResponse::create($url, $status, $headers)->send();
	}
}