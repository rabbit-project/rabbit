<?php
namespace Rabbit\Acl;


class Resource {

	/**
	 * @var Action[]
	 */
	private $_actions = array();

	public function addAction($action) {
		if(is_string($action))
			$action = new Action($action);

		$this->_actions[] = $action;
		return $this;
	}

	/**
	 * Verifica se uma determinada action existe
	 * @param string $actionName
	 * @return bool
	 */
	public function hasAction($actionName){
		foreach($this->_actions as $action)
			if($action->getName() == $actionName)
				return true;
		return false;
	}

	public function getAction(){

	}
}