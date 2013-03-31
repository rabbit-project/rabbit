<?php
namespace Rabbit\View;

use Rabbit\View\Exception\ViewException;
use Rabbit\View\Renderer\HtmlRender;
use Rabbit\View\Renderer\JsonRender;
use Rabbit\View\Renderer\RenderInterface;
use Rabbit\View\Renderer\XmlRender;
use Rabbit\View\ViewRenderType;

class ViewRenderFactory {

	/**
	 * @param ViewRenderType $type
	 * @param array          $config
	 *
	 * @return RenderInterface
	 * @throws Exception\ViewException
	 */
	public static function getRender(ViewRenderType $type, array $config = array()) {
        switch ($type){
			case ViewRenderType::get("HTML"):
				return new HtmlRender($config);
				break;
			case ViewRenderType::get("JSON"):
				return new JsonRender($config);
				break;
			case ViewRenderType::get("XML");
				return new XmlRender($config);
				break;
			default:
				throw new ViewException(sprintf('Não há implementação de renderização para <strong>%s</strong>', $type));
				break;
        }
    }

}