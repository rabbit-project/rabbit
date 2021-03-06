<?php
namespace Rabbit\Lang;

use Rabbit\Lang\Exception\EnumException;

/**
 * Classe base para php enumeration
 * @author Erick Leão <erickleao@rabbitcms.com.br>
 */
abstract class Enum {
   
	private $_name;
	private $_value;

	/**
	 * @param $name
	 * @return self
	 * @throws Exception\EnumException
	 */
	public static function get($name) {
		if(!self::isExists($name))
			throw new EnumException(sprintf("A constante %s não foi encontrada no ENUMType %s", $name, get_called_class()));
		
		$value = constant('static::' . $name);
		return new static($name, $value);
	}
	
	/**
	 * Retorna o nome da constant através do seu valor
	 * @param mixed $value
     * @return int|null|string
     */
	public static function getNameForValue($value) {
		$ref = new \ReflectionClass(get_called_class());
		$constans = $ref->getConstants();
		
		if(!$constans)
			return null;
		
		foreach($constans as $consName => $consValue){
			if($consValue == $value)
				return $consName;
		}
		
		return null;
	}
	
	final private function __construct($name, $value) {
		$this->_name = $name;
		$this->_value = $value;
	}

	final private function __clone(){ }
	
	/**
	 * Verifica se a constante existe
	 * @param string $name
	 * @return boolean
	 */
	public static function isExists($name) {
		return defined('static::' . $name);
	}
	
	/**
	 * Retorna o valor da constante
	 */
	public function getValue() {
		return $this->_value;
	}
	
	/**
	 * Retorna o nome da constante
	 */
	public function getName() {
		return $this->_name;
	}
	
	/**
	 * Compara se é igual
	 * 
	 * @param self $obj
	 * @return boolean
	 */
	public function equals(self $obj) {
		return $this == $obj;
	}

    /**
     * Retorna a constante selecionado
     * @return string
     */
    public function __toString() {
		return $this->_name;
	}
}