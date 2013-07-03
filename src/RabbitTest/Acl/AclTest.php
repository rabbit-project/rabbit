<?php
namespace RabbitTest\Acl;


use Rabbit\Acl\AclManager;
use Rabbit\Acl\Action;
use Rabbit\Acl\Actor;
use Rabbit\Acl\Resource;

class AclTest extends \PHPUnit_Framework_TestCase{

	/**
	 * Testando a criação de recurso
	 */
	public function testCreateAResourceX(){
		$resource = AclManager::createResource('ResourceX');
		$this->assertInstanceOf('Rabbit\Acl\Resource',$resource);
	}

	/**
	 * Testando exception para a criação de recurso duplicado
	 * @depends testCreateAResourceX
	 * @expectedException Rabbit\Acl\Exception\AclResourceExistException
	 */
	public function testExpectedExceptionForCreateResourceExists(){
		AclManager::createResource('ResourceX');
	}

	/**
	 * Verifica se existe o ResourceX
	 * @depends testCreateAResourceX
	 */
	public function testTheResourceXWasCreated(){
		$this->assertTrue(AclManager::hasResource('ResourceX'));
	}

	/**
	 * Testando exception para recuros não encontrados
	 * @expectedException Rabbit\Acl\Exception\AclResourceNotFoundException
	 */
	public function testExpectedExceptionForResourceNotFound(){
		AclManager::getResource("RecursoNaoExiste");
	}

	/**
	 * Adicionando uma ação para o recurso criado
	 * @depends testCreateAResourceX
	 */
	public function testAddingActionForResource(){
		/** @var Resource $resource */
		$resource = AclManager::getResource("ResourceX");
		$resource->addAction(new Action('edit'));
		$resource->addAction(new Action('create'));
		$resource->addAction(new Action('update'));

		$this->assertTrue(AclManager::getResource("ResourceX")->hasAction('edit'));
		$this->assertTrue(AclManager::getResource("ResourceX")->hasAction('create'));
		$this->assertTrue(AclManager::getResource("ResourceX")->hasAction('update'));
		$this->assertFalse(AclManager::getResource("ResourceX")->hasAction('fail'));
	}

	/**
	 * Criando um ator
	 */
	public function testCreateAActor() {
		$actor = new Actor('Ator1', 'Descrição Ator');

		$this->assertInstanceOf('Rabbit\Acl\Actor', $actor);
		$this->assertEquals('Ator1', $actor->getName());
		$this->assertEquals('Descrição Ator', $actor->getDescription());
	}

	/**
	 * Criando um ator e adicionando um parente
	 */
	public function testCreateAActorWithParent() {
		$actor1 = new Actor('Ator1');
		$actor2 = new Actor('Ator2', 'Ator2 é parente Ator1', array($actor1));

		$actorCurrent = $actor2->getActorsParents()[0];
		$this->assertCount(1, $actor2->getActorsParents());
		$this->assertInstanceOf('Rabbit\Acl\Actor', $actorCurrent);
		$this->assertEquals('Ator1', $actorCurrent);
	}

	/**
	 * Adicionando permissão para um ator
	 * @depends testCreateAResourceX
	 */
	public function testAddingGrantPermissionForActor() {
		$actor = new Actor('Ator');

		$resource = AclManager::getResource('ResourceX');
		$resource->addGrantPermission($actor, array('edit'));
		$this->assertTrue($resource->hasPermission('Ator', 'edit'));

	}

	/**
	 * Verificando se um ator possue um determinada ação em um recurso
	 * @depends testAddingGrantPermissionForActor
	 */
	public function testGrantedPermissionForAActor() {
		$this->assertTrue(AclManager::hasGrant('Ator', 'edit', 'ResourceX'));
	}

	/**
	 * Testa permissão herdado
	 * @depends testAddingGrantPermissionForActor
	 */
	public function testPermissionInheritedOfParent() {
		$resource = AclManager::getResource('ResourceX');
		$actor = $resource->getActor('Ator');
		$actor2 = new Actor('Ator2', null, array($actor));
		$resource->addGrantPermission($actor2, array('create'));
		$this->assertTrue($resource->hasPermission('Ator2', 'create'));
		$this->assertTrue($resource->hasPermission('Ator2', 'edit'));
		$this->assertFalse($resource->hasPermission('Ator2', 'update'));
	}

	public function testCompleted() { }
}