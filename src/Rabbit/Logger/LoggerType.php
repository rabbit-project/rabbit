<?php
namespace Rabbit\Logger;

use Rabbit\Lang\Enum;

/**
 * Classe Enum TYPE para Logger
 * @author Erick Leao <erickleao@rabbit-cms.com.br>
 */
final class LoggerType extends Enum {
	const RABBIT 	= 1;
	const DEBUG 	= 2;
	const INFO 		= 3;	
	const WARN		= 4;
	const ERROR 	= 5;
}