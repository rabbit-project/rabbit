<?php
namespace Rabbit\Acl;


use Rabbit\Acl\Exception\AclResourceActionNotFoundException;

class Resource {

	/**
	 * @var Action[]
	 */
	private $_actions = array();

	private $_actors = array();

	/**
	 * @var array
	 */
	private $_grants = array();

	/**
	 * @param string|Actor $action
	 *
	 * @return Resource
	 */
	public function addAction($action) {
		if(is_string($action))
			$action = new Action($action);

		$this->_actions[] = $action;
		return $this;
	}

	/**
	 * Verifica se uma determinada action existe
	 *
	 * @param string $actionName
	 *
	 * @return bool
	 */
	public function hasAction($actionName){
		foreach($this->_actions as $action)
			if($action == $actionName)
				return true;
		return false;
	}

	/**
	 * @param $actionName
	 *
	 * @return Action
	 * @throws Exception\AclResourceActionNotFoundException
	 */
	public function getAction($actionName){
		foreach($this->_actions as $action){
			if($action == $actionName){
				return $action;
			}
		}

		throw new AclResourceActionNotFoundException(sprintf('A ação %s não foi encontrada', $actionName));
	}

	/**
	 * @param string|Actor $actor
	 * @param array $actions
	 *
	 * @return $this
	 */
	public function addGrantPermission($actor, array $actions = null){
		if(is_string($actor))
			$actor =  new Actor($actor);

		$this->_actors[] = $actor;

		if($actions===null)
			$actions = $this->_actions;

		foreach($actions as $action){
			$act = $this->getAction($action);
			$this->_grants[$action][] = $actor;
		}

		return $this;
	}

	/**
	 * @param string  $actor
	 * @param string $action
	 *
	 * @return bool
	 */
	public function hasPermission($actor, $action) {
		if(!$this->hasAction($action) || !isset($this->_grants[$action]))
			return false;

		$actor = $this->getActor($actor);

		foreach($this->_grants[$action] as $actorGrant){

			if($actorGrant == $actor){
				return true;
			}else if($actor->getActorsParents() != null){
				foreach($actor->getActorsParents() as $actorParent){
					if($this->hasPermission($actorParent->getName(), $action))
						return true;
				}
			}
		}

		return false;

	}

	/**
	 * @param $actor
	 *
	 * @return null|Actor
	 */
	public function getActor($actor) {

		foreach($this->_actors as $atc)
			if($atc == $actor)
				return $atc;

		return null;
	}


}