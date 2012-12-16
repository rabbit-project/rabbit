<?php
namespace RabbitTest\Service;

use Rabbit\Service\ServiceLocator;

class ServiceLocaterTest extends \PHPUnit_Framework_TestCase {
	
	public function testRegisterService(){
		
		ServiceLocator::register("Rabbit\Service", function() {
			return "servico";
		});
		
		$this->assertTrue(ServiceLocator::isRegistred("Rabbit\Service"));
		$this->assertEquals("servico", ServiceLocator::getService("Rabbit\Service"));
	}
	
}
