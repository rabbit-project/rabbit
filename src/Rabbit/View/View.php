<?php

namespace Rabbit\View;

use Rabbit\Application\Front;
use Rabbit\View\Exception\ViewAcceptNotAvailableException;
use Rabbit\View\ViewRenderFactory;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class View
 * @package Rabbit\View
 * @see http://guides.rubyonrails.org/layouts_and_rendering.html
 */
class View {

    public static function render($args = NULL, array $config = array()) {

        $request = Front::getInstance()->getRequest();

		$formatDefault = isset($config['formatDefault'])? $config['formatDefault'] : 'html';

		$type = $request->isXmlHttpRequest()? 'json' : $request->getRequestFormat(strtolower($formatDefault));

		if(isset($config['accepts']) && !in_array($type, $config['accepts']) || !isset($config['accepts']) && $type != 'html')
			throw new ViewAcceptNotAvailableException(sprintf("O Accept <strong>%s</strong> solicitado não está ativo", $type));

		$config['args'] = $args;

		return ViewRenderFactory::getRender(ViewRenderType::get(strtoupper($type)), $config);
    }

    public static function redirectTo(array $config) {
        $config["module"];
        $config["namespace"];
        $config["controller"];
        $config["action"];
    }

}