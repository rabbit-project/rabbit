<?php
namespace Rabbit\View;

use Rabbit\Lang\Enum;

/**
 * Class ViewRenderType
 * Tipo ViewRender
 * @package Rabbit\View
 * @method static ViewRenderType get(\string $name)
 */
final class ViewRenderType extends Enum{
    CONST HTML  = 1;
    CONST JSON  = 2;
    CONST XML   = 3;
}
