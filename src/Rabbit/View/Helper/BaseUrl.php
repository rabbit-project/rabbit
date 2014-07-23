<?php
namespace Rabbit\View\Helper;


use Rabbit\Application\Front;

class BaseUrl extends HelperAbstract{

	public function baseUrl($url=NULL, $isRoot=false) {
		$request = $this->getRequest();

		preg_match_all('#(?<bases>[^/]+)[^(index\.php)]#', $request->getScriptName(), $matcher);

		$urlMount = ($request->getBaseUrl() != '')? ltrim($request->getBaseUrl(), '/') . '/' : '';
		
		if($isRoot){
			
			$iterator = new \ArrayIterator(explode('/',$request->getScriptName()));
			$iterator->offsetUnset(0);
			$iterator->offsetUnset($iterator->count());
			$iterator->offsetUnset($iterator->count());
			
			$urlMount = implode('/', $iterator->getArrayCopy()) . '/';
			
		}

		if(empty($urlMount) && isset($matcher['bases'])){
			foreach($matcher['bases'] as $base)
				if(preg_match(sprintf('#%s#',preg_quote($base)), $request->getRequestUri()))
					$urlMount .= sprintf('%s/',$base);
		}

		return sprintf('http://%s/%s%s',  $request->getHttpHost(), $urlMount, $url);

	}

}