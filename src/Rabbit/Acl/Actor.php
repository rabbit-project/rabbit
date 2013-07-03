<?php

namespace Rabbit\Acl;

/**
 * Class Actor
 * Classe responsavel pelo armazenamento das informações do Ator
 * @package Rabbit\Acl
 */
class Actor {

	/**
	 * @var string
	 */
	private $_name;

	/**
	 * @var string
	 */
	private $_description;

	/**
	 * @var Actor[]
	 */
	private $_actorsParents = array();

	public function __construct($name, $descript = NULL, array $actorParents = array()) {
		$this->_name = $name;
		$this->_description = $descript;
		$this->_actorsParents = $actorParents;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->_name;
	}

	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->_description;
	}

	/**
	 * @return Actor[]
	 */
	public function getActorsParents() {
		return $this->_actorsParents;
	}

	public function __toString()
	{
		return $this->_name;
	}

}