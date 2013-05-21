<?php
namespace RabbitTest\Acl;


use Rabbit\Acl\AclManager;
use Rabbit\Acl\Action;

class AclTest extends \PHPUnit_Framework_TestCase{

	/**
	 * Testando a criação de recurso
	 */
	public function testCreateAResource(){
		$resource = AclManager::createResource('ResourceX');
		$this->assertInstanceOf('Rabbit\Acl\Resource',$resource);
	}

	/**
	 * Testando exception para a criação de recurso duplicado
	 * @depends testCreateAResource
	 * @expectedException Rabbit\Acl\Exception\AclResourceExistException
	 */
	public function testExpetedExceptionForCreateResourceExists(){
		AclManager::createResource('ResourceX');
	}

	/**
	 * Verifica se existe o ResourceX
	 * @depends testCreateAResource
	 */
	public function testHasResourceXCreated(){
		$this->assertTrue(AclManager::hasResource('ResourceX'));
	}

	/**
	 * Testando exception para recuros não encontrados
	 * @expectedException Rabbit\Acl\Exception\AclResourceNotFoundException
	 */
	public function testExpetedExceptionForResourceNotFound(){
		AclManager::getResource("RecursoNaoExiste");
	}

	/**
	 * Adicionando uma ação para o recurso criado
	 * @depends testCreateAResource
	 */
	public function testAddingActionForResource(){
		/** @var Resource $resource */
		$resource = AclManager::getResource("ResourceX");
		$resource->addAction(new Action('edit'));

		$this->assertTrue(AclManager::getResource("ResourceX")->hasAction('edit'));
	}

}