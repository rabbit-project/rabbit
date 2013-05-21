<?php
namespace Rabbit\Acl;


use Rabbit\Acl\Exception\AclResourceExistException;
use Rabbit\Acl\Exception\AclResourceNotExistException;
use Rabbit\Acl\Exception\AclResourceNotFoundException;

abstract class AclManager {

	/**
	 * @var Resource[]
	 */
	private static $_resources = array();

	/**
	 * Criando um novo recurso
	 * @param string $name
	 * @return Resource
	 * @throws Exception\AclResourceExistException
	 */
	public static function createResource($name) {
		if(self::hasResource($name))
			throw new AclResourceExistException(sprintf('O recurso <strong>%s</strong> já existe', $name));

		self::$_resources[$name] = new Resource($name);

		return self::$_resources[$name];
	}

	/**
	 * Retorna true se existe o resource já criado
	 * @param string $name
	 * @return bool
	 */
	public static function hasResource($name) {
		return isset(self::$_resources[$name]);
	}

	/**
	 * Limpa resources
	 */
	public static function clear() {
		self::$_resources = array();
	}

	/**
	 * @param $name
	 *
	 * @return Resource
	 * @throws Exception\AclResourceNotFoundException
	 */
	public static function getResource($name){
		if(!self::hasResource($name))
			throw new AclResourceNotFoundException(sprintf('O recurso <strong>%s</strong> não foi encontrado', $name));

		return self::$_resources[$name];
	}

}