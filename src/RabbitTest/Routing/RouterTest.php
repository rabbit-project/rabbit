<?php
namespace RabbitTest\Routing;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Request;

class RouterTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * @var Request
	 */
	protected $_request;
	
	/**
	 * @var Response
	 */
	protected $_response;
	
	public function setUp() {
		$this->_request = new Request();
		$this->_response = new Response();
	}
	
}