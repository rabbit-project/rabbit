<?php
namespace Rabbit\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

use Rabbit\ServiceLocator;

use Rabbit\Application;

use Rabbit\View;
use Rabbit\View\ViewInterface;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractController {

	/**
	 * @var Request
	 */
	protected $request;

	/**
	 * @var Response
	 */
	protected $response;

	protected $renderer = true;

	protected $_prefix = "phtml";

	/**
	 * @var ViewInterface
	 */
	protected $view;

	public function __construct(Request $request, Response $response) {
		$this->request = $request;
		$this->response = $response;
		$this->executeInjectionDependence();
		$this->init();
	}

	public function init() { }

	protected function preDispatch() {}
	protected function postDispatch() {}

	public function dispatch($action) {
		$this->preDispatch();

		if(preg_match("#-#", $action)){
			$action = preg_replace_callback("#-(.)#", function($matche){
				return ucfirst($matche[1]);
			}, $action);
		}

		if(!method_exists($this, $action))
			throw new ActionNotFoundException(sprintf("Não foi possível encontrar a ação <strong>%s</strong> no controller: <strong>%s</strong>", $action, get_class($this)));

		$view = $this->$action();

		if($view instanceof View\Renderer\RenderInterface && $this->renderer){
			// se não for definido uma view no retorno é definido um padrão
			/*if(!$view)
				$view = new View();*/

			$content = $this->getResponse()->getContent();
			$content .= $view->render();
			$this->getResponse()->setContent($content);
			$this->getResponse()->prepare($this->getRequest());
		}

		$this->postDispatch();
	}

	/**
	 * @return Request
	 */
	public function getRequest() {
		return $this->request;
	}

	public function setRequest(Request $request) {
		$this->request = $request;
	}

	/**
	 * @return Response
	 */
	public function getResponse() {
		return $this->response;
	}

	public function setResponse(Response $response) {
		$this->response = $response;
	}

	public function executeInjectionDependence() {

		$args = array_merge($this->request->query->all(), $this->request->request->all());
		$this->dependenceArgsClass($args);

	}

	private function dependenceArgsClass($args, $ref = null) {
		$clazz = $ref != null ? $ref : $this;

		foreach($args as $key => $value) {
			$nameMethod = 'set' . ucfirst($key);

			if(!method_exists($clazz, $nameMethod))
				continue;

			$refMethod = new \ReflectionMethod($clazz, $nameMethod);

			if(is_array($value)){

				foreach($refMethod->getParameters() as $methodParam)
					if($methodParam->getClass()!=null){
						$refNew = method_exists($methodParam->getClass(),'newInstanceWithoutConstructor')? $methodParam->getClass()->newInstanceWithoutConstructor() : $methodParam->getClass()->newInstanceArgs();
						$clazz->$nameMethod($this->dependenceArgsClass($value, $refNew));
					}else{
						$clazz->$nameMethod($value);
					}
			}else{
				$clazz->$nameMethod($value);
			}
		}

		return $clazz;
	}
}