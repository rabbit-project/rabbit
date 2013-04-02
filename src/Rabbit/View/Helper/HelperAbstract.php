<?php
namespace Rabbit\View\Helper;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class HelperAbstract {
	
	/** @var Request **/
	protected $_request;
	
	/** @var Response **/
	protected $_response;
	
	public function __construct(Request $request, Response $response) {
		$this->setRequest($request);
		$this->setResponse($response);
		$this->init();
	}	
	
	/**
	 * @return Request
	 */
	public function getRequest() {
		return $this->_request;
	}

	/**
	 * @param Request $request
	 */
	public function setRequest(Request $request) {
		$this->_request = $request;
	}
	
	/**
	 * @return Response
	 */
	public function getResponse() {
		return $this->_response;
	}
	
	public function setResponse(Response $response) {
		$this->_response = $response;
	}

	public function init() {}
}