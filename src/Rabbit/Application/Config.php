<?php
namespace Rabbit\Application;

use Rabbit\Application\Exception\ApplicationException;
use Rabbit\Application\Exception\ApplicationFileNotFoundException;

class Config {
	
	public static function getDataSourceInfo($dataSourceName) {
		$uri = RABBIT_PATH_CONFIG . DS . "data-sources.config.php";

		if(!file_exists($uri))
			throw new ApplicationFileNotFoundException(sprintf('O arquivo de configuração de datasource não foi encontrado <strong>%s</strong>',$uri));

		$dataSource = include_once $uri;

		if(!isset($dataSource[$dataSourceName]))
			throw new ApplicationException(sprintf('Não foi possível encontrar o datasource <strong>%s</strong>', $dataSourceName));

		return $dataSource[$dataSourceName];
	}
	
	public static function installed() {
		
	}
	
}