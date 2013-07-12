<?php
namespace Rabbit\ORM;

use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManager as EM;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Tools\Setup;
use Rabbit\Application\Config;

abstract class EntityManager {

	public static $em = array();

	/**
	 * @param string $dataSourceName
	 *
	 * @return EM
	 */
	public static function getForDataSource($dataSourceName){
		if(isset(self::$em[$dataSourceName]))
			return self::$em[$dataSourceName];

		$configDataSource = Config::getDataSourceInfo($dataSourceName);

		$evm = new EventManager();

		if(isset($configDataSource['prefix'])){;
			$evm->addEventListener(Events::loadClassMetadata, new TablePrefix($configDataSource['prefix']));
		}

		$configAnnotation = Setup::createAnnotationMetadataConfiguration(array(), true);

		return EM::create($configDataSource, $configAnnotation, $evm);
	}

}

class TablePrefix
{
	protected $prefix = '';

	public function __construct($prefix)
	{
		$this->prefix = (string) $prefix;
	}

	public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
	{
		$classMetadata = $eventArgs->getClassMetadata();
		$classMetadata->table['name'] = $this->prefix . $classMetadata->table['name'];
		foreach ($classMetadata->getAssociationMappings() as $fieldName => $mapping) {
			if ($mapping['type'] == ClassMetadataInfo::MANY_TO_MANY) {
				$mappedTableName = $classMetadata->associationMappings[$fieldName]['joinTable']['name'];
				$classMetadata->associationMappings[$fieldName]['joinTable']['name'] = $this->prefix . $mappedTableName;
			}
		}
	}

}