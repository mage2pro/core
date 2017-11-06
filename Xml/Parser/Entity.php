<?php
namespace Df\Xml\Parser;
use Df\Xml\X;
class Entity extends \Df\Core\O {
	/**
	 * Возвращает единственного ребёнка с указанными именем.
	 * Контролирует отсутствие других детей с указанными именем.
	 * @param string $name
	 * @param bool $required [optional]
	 * @return X|null
	 * @throws \Df\Core\Exception
	 */
	function child($name, $required = false) {
		if (!isset($this->{__METHOD__}[$name])) {
			$this->{__METHOD__}[$name] = df_n_set($this->e()->child($name, $required));
		}
		return df_n_get($this->{__METHOD__}[$name]);
	}

	/**
	 * @used-by \Df\Xml\Parser\Collection::addItem()
	 * @return string|null
	 */
	function getName() {return $this->getId();}

	/**
	 * @param string $path
	 * @param bool $throw [optional]
	 * @return float|null
	 */
	function descendF($path, $throw = false) {
		return $this->descendWithCast($path, 'df_float', 'вещественное', $throw);
	}

	/**
	 * @param string $path
	 * @param bool $throw [optional]
	 * @return int|null
	 */
	function descendI($path, $throw = false) {
		return $this->descendWithCast($path, 'df_int', 'целое', $throw);
	}

	/**
	 * @param string $path
	 * @param bool $throw [optional]
	 * @return string|null
	 * @throws \Df\Core\Exception
	 */
	function descendS($path, $throw = false) {
		if (!isset($this->{__METHOD__}[$path])) {
			$element = $this->e()->descend($path); /** @var X|bool $element */
			$found = !is_null($element); /** @var bool $found */
			if (!$found && $throw) {
				df_error('В документе XML отсутствует путь «%s».', $path);
			}
			$this->{__METHOD__}[$path] = df_n_set($found ? df_leaf_s($element) : null);
		}
		return df_n_get($this->{__METHOD__}[$path]);
	}

	/** @return X */
	function e() {return dfc($this, function() {return df_xml_parse($this[self::$P__E]);});}

	/**
	 * @param string $name
	 * @param string|int|array|float|null $defaultValue [optional]
	 * @return mixed
	 */
	function getAttribute($name, $defaultValue = null) {
		df_param_sne($name, 0);
		/** @var string|int|array|float|null $result */
		$result = $this->getAttributeInternal($name);
		return !is_null($result) ? $result : $defaultValue;
	}

	/**
	 * @param string $childName
	 * @return bool
	 */
	function isChildComplex($childName) {
		df_param_sne($childName, 0);
		if (!isset($this->{__METHOD__}[$childName])) {
			/** @var X|null $child */
			$child = $this->child($childName, $isRequired = false);
			$this->{__METHOD__}[$childName] = $child && !df_check_leaf($child);
		}
		return $this->{__METHOD__}[$childName];
	}

	/**
	 * @param string $childName
	 * @return bool
	 */
	function isChildExist($childName) {
		if (!isset($this->{__METHOD__}[$childName])) {
			$this->{__METHOD__}[$childName] = df_xml_exists_child($this->e(), $childName);
		}
		return $this->{__METHOD__}[$childName];
	}

	/**
	 * @used-by \Df\Xml\Parser\Collection::getItems()
	 * @return bool
	 */
	function isValid() {return true;}

	/**
	 * 2015-08-16
	 * @used-by leafB()
	 * @used-by leafF()
	 * @used-by leafI()
	 * @used-by leafSne()
	 * @param string $name
	 * @param string|null|callable $default [optional]
	 * @param string $function [optional]
	 * @return string|null
	 */
	function leaf($name, $default = null, $function = 'df_leaf') {
		/** @var string $key */
		$key = "$name::$function";
		if (!isset($this->{__METHOD__}[$key])) {
			$this->{__METHOD__}[$key] = df_n_set(call_user_func($function, $this->e()->{$name}));
		}
		/** @var string|null $result */
		$result = df_n_get($this->{__METHOD__}[$key]);
		return df_if1(is_null($result), $default, $result);
	}

	/**
	 * 2015-08-16
	 * @see df_leaf_b()
	 * @param string $name
	 * @return bool
	 */
	function leafB($name) {return $this->leaf($name, null, 'df_leaf_b');}

	/**
	 * 2015-08-16
	 * Намеренно убрал параметр $default.
	 * @see df_leaf_f()
	 * @param string $name
	 * @return float
	 */
	function leafF($name) {return $this->leaf($name, null, 'df_leaf_f');}

	/**
	 * 2015-08-16
	 * @see df_leaf_i()
	 * @param string $name
	 * @return int
	 */
	function leafI($name) {return $this->leaf($name, null, 'df_leaf_i');}

	/**
	 * 2015-08-16
	 * @see df_leaf_sne()
	 * @param string $name
	 * @return string
	 */
	function leafSne($name) {return $this->leaf($name, null, 'df_leaf_sne');}

	/** @return string */
	protected function getXmlForReport() {return df_xml_report($this->e());}

	/**
	 * @param string $path
	 * @param callable $castFunction
	 * @param string $castName
	 * @param bool $throw [optional]
	 * @return mixed|null
	 */
	private function descendWithCast($path, callable $castFunction, $castName, $throw = false) {
		$resultAsText = $this->descendS($path, $throw); /** @var string|null $resultAsText */
		/** @var mixed|null $result */
		if (!df_nes($resultAsText)) {
			$result = call_user_func($castFunction, $resultAsText);
		}
		else {
			if ($throw) {
				df_error('В документе XML по пути «%s» требуется %s число, однако там пусто.', $castName, $path);
			}
			else {
				$result = null;
			}
		}
		return $result;
	}

	/**
	 * @param string $name
	 * @return string|int|array|float|null
	 */
	private function getAttributeInternal($name) {
		if (!isset($this->{__METHOD__}[$name])) {
			$this->{__METHOD__}[$name] = df_n_set($this->e()->getAttribute($name));
		}
		return df_n_get($this->{__METHOD__}[$name]);
	}

	/**
	 * @override
	 * @see \Df\Core\O::_construct()
	 */
	protected function _construct() {
		parent::_construct();
		// параметр PARAM__SIMPLE_XML может быть как объектом, так и строкой.
	}
	/** @var string */
	protected static $P__E = 'e';
	/**
	 * Обратите внимание, что этот метод нельзя называть i(),
	 * потому что от класса Df_Core_Xml_Parser_Entity наследуются другие классы,
	 * и у наследников спецификация метода i() другая, что приводит к сбою интерпретатора PHP:
	 * «Strict Notice: Declaration of Df_Licensor_Model_File::i()
	 * should be compatible with that of Df_Core_Xml_Parser_Entity::i()»
	 *
	 * @used-by \Df\Xml\Parser\Collection::createItem()
	 * @used-by \Dfr\Core\Realtime\Dictionary\Layout::i()
	 * @static
	 * @param X|string $e
	 * @param string $class [optional]
	 * @param array(string => mixed) $params
	 * @return Entity
	 */
	static function entity($e, $class = __CLASS__, array $params = []) {
		return df_ic($class, __CLASS__, [self::$P__E => $e] + $params);
	}
}