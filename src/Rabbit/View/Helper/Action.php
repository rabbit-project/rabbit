<?php
namespace Rabbit\View\Helper;


use Rabbit\View\Exception\ViewHelperException;

class Action extends HelperAbstract{

	public function action($args){

		$module = ucfirst(isset($args["module"])? $args["module"] : $this->_request->get('module'));
		$namespace = ucfirst(isset($args["namespace"])? $args["namespace"] : $this->_request->get('namespace'));
		$controller = ucfirst(isset($args["controller"])? $args["controller"] : $this->_request->get('controller'));
		$action = (isset($args["action"])? $args["action"] : $this->_request->get('action'));

		$mapSelf 	= sprintf('%s\Namespaces\%s\Controller\%sController::%s', $module, $namespace, $controller, $action);
		$mapRequest = sprintf('%s\Namespaces\%s\Controller\%sController::%s', ucfirst($this->_request->get('module')),
			ucfirst($this->_request->get('namespace')), ucfirst($this->_request->get('controller')), $this->_request->get('action'));

		if($mapSelf == $mapRequest)
			throw new ViewHelperException(sprintf('O mapeamento do helper action não pode ser o mesmo escopo de sua execução - escopo: <strong>%s</strong>', $mapRequest));

		$this->_request->attributes->add($args);


		$clsName = sprintf('%s\Namespaces\%s\Controller\%sController', $module, $namespace, $controller);


		/** @var AbstractController $clazz */
		$clazz = new $clsName($this->_request, $this->_response);
		return $clazz->dispatch($action);
	}

}