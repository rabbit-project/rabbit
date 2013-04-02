<?php

namespace Rabbit\View;

use Doctrine\Common\ClassLoader;
use Rabbit\Application\Front;
use Rabbit\Service\ServiceLocator;
use Rabbit\View\Exception\ViewRenderException;
use Rabbit\View\Helper\HelperAbstract;
use Rabbit\View\ViewRenderFactory;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class View
 * @package Rabbit\View
 * @see http://guides.rubyonrails.org/layouts_and_rendering.html
 */
class View {

	private static $_helpersMap = array('Rabbit\View\Helper');
	private static $_helps = array();

    public static function render($args = NULL, array $config = array()) {

        $request = Front::getInstance()->getRequest();

		$formatDefault = isset($config['formatDefault'])? $config['formatDefault'] : 'html';

		$type = $request->isXmlHttpRequest()? 'json' : $request->getRequestFormat(strtolower($formatDefault));

		if(isset($config['accepts']) && !in_array($type, $config['accepts']) || !isset($config['accepts']) && $type != 'html')
			throw new ViewRenderException(sprintf("O Accept <strong>%s</strong> solicitado não está ativo", $type));

		$config['args'] = $args;

		return ViewRenderFactory::getRender(ViewRenderType::get(strtoupper($type)), $config);
    }

    public static function redirectTo(array $config) {
        $config["module"];
        $config["namespace"];
        $config["controller"];
        $config["action"];
    }

	public static function registerHelper($namespace) {
		self::$_helpersMap[] = $namespace;
	}

	public static function getHelper($helper, array $args = array()) {
		$request = Front::getInstance()->getRequest();
		$response = Front::getInstance()->getResponse();

		foreach(array_reverse(self::$_helpersMap) as $helpMap){
			$fileURI = $helpMap . '\\' . ucfirst($helper) ;

			if(isset(self::$_helps[$fileURI]))
				return call_user_func_array(array(self::$_helps[$fileURI],$helper), $args);

			/** @var $load \Composer\Autoload\ClassLoader */
			$load = ServiceLocator::getService('Rabbit\Load');
			if($load->loadClass($fileURI)){
				/** @var $helper HelperAbstract */
				$helperCls = new $fileURI($request, $response);
				self::$_helps[$fileURI] = $helperCls;
				return call_user_func_array(array($helperCls,$helper), $args);
			}
		}

	}

}