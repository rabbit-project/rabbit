<?php
namespace Rabbit\View\Helper;


use Rabbit\Application\Front;

class BaseUrl extends HelperAbstract{

	public function baseUrl($url=NULL) {
		$request = $this->getRequest();

		preg_match_all('#(?<bases>[^/]+)[^(index\.php)]#', $request->getScriptName(), $matcher);

		$urlMount = ($request->getBaseUrl() != '')? ltrim($request->getBaseUrl(), '/') . '/' : '';

		if(empty($urlMount) && isset($matcher['bases'])){
			foreach($matcher['bases'] as $base)
				if(preg_match(sprintf('#%s#',preg_quote($base)), $request->getRequestUri()))
					$urlMount .= sprintf('%s/',$base);
		}

		return sprintf('http://%s/%s%s',  $request->getHttpHost(), $urlMount, $url);

	}

}