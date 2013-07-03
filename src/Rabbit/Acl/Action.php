<?php
namespace Rabbit\Acl;

/**
 * Class Action
 * Class responsavel por armazenar informação de Ação
 * @package Rabbit\Acl
 */
class Action {

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @param string $name
	 */
	public function __construct($name) {
		$this->name = $name;
	}

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	public function __toString(){
		return $this->getName();
	}
}