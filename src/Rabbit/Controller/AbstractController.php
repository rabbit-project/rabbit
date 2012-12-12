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
		$this->init();
	}
	
	public function init() { }
	
	protected function preDispatch() {}
	protected function postDispatch() {}
	
	public function dispatch($action) {
		$this->preDispatch();
		
		if(!method_exists($this, $action))
			throw new ActionNotFoundException(sprintf("Não foi possível encontrar a ação <strong>%s</strong> no controller: <strong>%s</strong>", $action, get_class($this)));
			
		$view = $this->$action();
		
		if($this->renderer){
			// se não for definido uma view no retorno é definido um padrão
			if(!$view)
				$view = new View();
			
			$view->setRequest($this->getRequest());
			
			$md = $this->getRequest()->get("module");
			$ns = $this->getRequest()->get("namespace");
			$cr = $this->getRequest()->get("controller");
			$ac = $this->getRequest()->get("action");
			
			$fileURI =  RABBIT_PATH_MODULE . DS . ucfirst($md) . DS .'view' . DS . $ns . DS . $cr . DS . $ac ."." . $this->_prefix;
			
			$content = $this->getResponse()->getContent();
			$content .= $view->render($fileURI);
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
}
