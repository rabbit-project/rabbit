<?php
namespace Rabbit\View\Helper;

use Rabbit\Service\ServiceLocator;

class Translator extends HelperAbstract{

	/**
	 * @var \Symfony\Component\Translation\Translator
	 */
	private $translate;

	public function translator(){
		$this->translate = ServiceLocator::getService('Rabbit\Service\Translator');
		return $this;
	}

	public function trans($id, array $parameters = array(), $domain = null, $locale = null){
		$domain = ($domain != null)? $domain : ucfirst($this->getRequest()->get('module'));
		return $this->translate->trans($id, $parameters, $domain, $locale);
	}

}