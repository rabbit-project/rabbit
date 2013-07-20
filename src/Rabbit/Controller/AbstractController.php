<?php
namespace Rabbit\Controller;

use Rabbit\Application;
use Rabbit\Controller\Exception\ActionNotFoundException;
use Rabbit\Layout\Layout;
use Rabbit\ServiceLocator;
use Rabbit\View;
use Rabbit\View\ViewInterface;
use Rabbit\Event\EventManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
	 * @var Layout
	 */
	protected $layout;

	protected $renderLayout = true;

	/**
	 * @var ViewInterface
	 */
	protected $view;

	public function __construct(Request $request, Response $response) {
		$this->request = $request;
		$this->response = $response;
		$this->executeInjectionDependence();

		EventManager::fire('Rabbit\Event\Controller\Start', array($this));

		$this->layoutInit();
		$this->init();
	}

	public function init() { }
	private function layoutInit() {
		$this->layout = new Layout();
		$this->layout->setLayout(RABBIT_LAYOUT_THEME_DEFAULT);
	}

	protected function preDispatch() {}
	protected function postDispatch() {}

	public function dispatch($action) {
		$this->preDispatch();
		EventManager::fire('Rabbit\Event\Controller\BeforeDispatched', array($this, $action));

		if(preg_match("#-#", $action)){
			$action = preg_replace_callback("#-(.)#", function($matche){
				return ucfirst($matche[1]);
			}, $action);
		}

		$action = $action . 'Action';

		if(!method_exists($this, $action))
			throw new ActionNotFoundException(sprintf("Não foi possível encontrar a ação <strong>%s</strong> no controller: <strong>%s</strong>", $action, get_class($this)));

		$actionReturn = $this->$action();
		EventManager::fire('Rabbit\Event\Controller\ActionCalled', array($this, $actionReturn));

		if($actionReturn instanceof View\View && $this->renderer){

			$content = $this->getResponse()->getContent();
			$content .= $actionReturn->render();
			EventManager::fire('Rabbit\Event\Controller\RenderViewCalled', array($this, $content));

			# renderizando layout
			if($this->renderLayout){
				$content = $this->layout->render($content);
				EventManager::fire('Rabbit\Event\Controller\RenderLayoutCalled', array($this, $content));
			}

			$this->getResponse()->setContent($content);
			$this->getResponse()->prepare($this->getRequest());
		}

		$this->postDispatch();
		EventManager::fire('Rabbit\Event\Controller\AfterDispatched', array($this, $action));
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

	private function executeInjectionDependence() {

		$args = array_merge($this->request->query->all(), $this->request->request->all());
		$this->dependenceArgsClass($args);

	}

	private function dependenceArgsClass($args, $ref = NULL) {
		$clazz = $ref != NULL ? $ref : $this;

		foreach($args as $key => $value) {
			$nameMethod = 'set' . ucfirst($key);

			if(!method_exists($clazz, $nameMethod))
				continue;

			$refMethod = new \ReflectionMethod($clazz, $nameMethod);

			if(is_array($value)){

				foreach($refMethod->getParameters() as $methodParam)
					if($methodParam->getClass()!=NULL){
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