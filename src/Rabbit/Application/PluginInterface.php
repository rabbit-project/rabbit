<?php
namespace Rabbit\Application;

use Rabbit\Routing\Router;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

interface PluginInterface {
	
	public function __construct(Request $request, Response $response);
	public function execute();
	
}