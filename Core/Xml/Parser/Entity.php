<?php
namespace Df\Core\Xml\Parser;
class Entity extends \Df\Core\O {
	/**
	 * Возвращает единственного ребёнка с указанными именем.
	 * Контролирует отсутствие других детей с указанными именем.
	 * @param string $name
	 * @param bool $required [optional]
	 * @return \Df\Core\Sxe|null
	 * @throws \Df\Core\Exception
	 */
	public function child($name, $required = false) {
		if (!isset($this->{__METHOD__}[$name])) {
			$this->{__METHOD__}[$name] = df_n_set($this->e()->child($name, $required));
		}
		return df_n_get($this->{__METHOD__}[$name]);
	}

	/**
	 * @used-by \Df\Core\Xml\Parser\Collection::addItem()
	 * @return string|null
	 */
	public function getName() {return $this->getId();}

	/**
	 * @param string $path
	 * @param bool $throw [optional]
	 * @return float|null
	 */
	public function descendF($path, $throw = false) {
		return $this->descendWithCast($path, 'df_float', 'вещественное', $throw);
	}

	/**
	 * @param string $path
	 * @param bool $throw [optional]
	 * @return int|null
	 */
	public function descendI($path, $throw = false) {
		return $this->descendWithCast($path, 'df_int', 'целое', $throw);
	}

	/**
	 * @param string $path
	 * @param bool $throw [optional]
	 * @return string|null
	 * @throws \Df\Core\Exception
	 */
	public function descendS($path, $throw = false) {
		if (!isset($this->{__METHOD__}[$path])) {
			/** @var \Df\Core\Sxe|bool $element */
			$element = $this->e()->descend($path);
			/** @var bool $found */
			$found = !is_null($element);
			if (!$found && $throw) {
				df_error('В документе XML отсутствует путь «%s».', $path);
			}
			$this->{__METHOD__}[$path] = df_n_set($found ? df_leaf_s($element) : null);
		}
		return df_n_get($this->{__METHOD__}[$path]);
	}

	/** @return \Df\Core\Sxe */
	public function e() {
		if (!isset($this->{__METHOD__})) {
			/** @var \Df\Core\Sxe $result */
			$result = $this->_getData(self::$P__E);
			if (is_string($result)) {
				$result = df_xml($result);
			}
			if (!$result instanceof \Df\Core\Sxe) {
				df_error('Вместо \Df\Core\Sxe получена переменная типа «%s».', gettype($result));
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $name
	 * @param string|int|array|float|null $defaultValue [optional]
	 * @return mixed
	 */
	public function getAttribute($name, $defaultValue = null) {
		df_param_string_not_empty($name, 0);
		/** @var string|int|array|float|null $result */
		$result = $this->getAttributeInternal($name);
		return !is_null($result) ? $result : $defaultValue;
	}

	/**
	 * @param string $childName
	 * @return bool
	 */
	public function isChildComplex($childName) {
		df_param_string_not_empty($childName, 0);
		if (!isset($this->{__METHOD__}[$childName])) {
			/** @var \Df\Core\Sxe|null $child */
			$child = $this->child($childName, $isRequired = false);
			$this->{__METHOD__}[$childName] = $child && !df_check_leaf($child);
		}
		return $this->{__METHOD__}[$childName];
	}

	/**
	 * @param string $childName
	 * @return bool
	 */
	public function isChildExist($childName) {
		if (!isset($this->{__METHOD__}[$childName])) {
			$this->{__METHOD__}[$childName] = df_xml_exists_child($this->e(), $childName);
		}
		return $this->{__METHOD__}[$childName];
	}

	/**
	 * @used-by \Df\Core\Xml\Parser\Collection::getItems()
	 * @return bool
	 */
	public function isValid() {return true;}

	/**
	 * 2015-08-16
	 * @used-by leafB()
	 * @used-by leafF()
	 * @used-by leafI()
	 * @used-by leafSne()
	 * @param string $name
	 * @param string|null $default [optional]
	 * @param string $function [optional]
	 * @return string|null
	 */
	public function leaf($name, $default = null, $function = 'df_leaf') {
		/** @var string $key */
		$key = $name . '::' . $function;
		if (!isset($this->{__METHOD__}[$key])) {
			$this->{__METHOD__}[$key] = df_n_set(call_user_func($function, $this->e()->{$name}));
		}
		/** @var string|null $result */
		$result = df_n_get($this->{__METHOD__}[$key]);
		return is_null($result) ? $default : $result;
	}

	/**
	 * 2015-08-16
	 * @see df_leaf_b()
	 * @param string $name
	 * @return bool
	 */
	public function leafB($name) {return $this->leaf($name, null, 'df_leaf_b');}

	/**
	 * 2015-08-16
	 * Намеренно убрал параметр $default.
	 * @see df_leaf_f()
	 * @param string $name
	 * @return float
	 */
	public function leafF($name) {return $this->leaf($name, null, 'df_leaf_f');}

	/**
	 * 2015-08-16
	 * @see df_leaf_i()
	 * @param string $name
	 * @return int
	 */
	public function leafI($name) {return $this->leaf($name, null, 'df_leaf_i');}

	/**
	 * 2015-08-16
	 * @see df_leaf_sne()
	 * @param string $name
	 * @return string
	 */
	public function leafSne($name) {return $this->leaf($name, null, 'df_leaf_sne');}

	/** @return string */
	protected function getXmlForReport() {return df_xml_report($this->e());}

	/**
	 * @param string $path
	 * @param callable $castFunction
	 * @param string $castName
	 * @param bool $throw [optional]
	 * @return mixed|null
	 */
	private function descendWithCast($path, $castFunction, $castName, $throw = false) {
		/** @var string|null $resultAsText */
		$resultAsText = $this->descendS($path, $throw);
		/** @var mixed|null $result */
		if (!is_null($resultAsText) && !df_empty_string($resultAsText)) {
			$result = call_user_func($castFunction, $resultAsText);
		}
		else {
			if ($throw) {
				df_error(
					'В документе XML по пути «%s» требуется %s число, однако там пусто.'
					, $castName
					, $path
				);
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
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		// параметр PARAM__SIMPLE_XML может быть как объектом, так и строкой.
	}
	const _C = __CLASS__;
	/** @var string */
	protected static $P__E = 'e';
	/**
	 * Обратите внимание, что этот метод нельзя называть i(),
	 * потому что от класса Df_Core_Xml_Parser_Entity наследуются другие классы,
	 * и у наследников спецификация метода i() другая, что приводит к сбою интерпретатора PHP:
	 * «Strict Notice: Declaration of Df_Licensor_Model_File::i()
	 * should be compatible with that of Df_Core_Xml_Parser_Entity::i()»
	 *
	 * @used-by \Df\Core\Xml\Parser\Collection::createItem()
	 * @used-by \Dfr\Translation\Realtime\Dictionary\Layout::i()
	 * @static
	 * @param \Df\Core\Sxe|string $e
	 * @param string $class [optional]
	 * @param array(string => mixed) $params
	 * @return Entity
	 */
	public static function entity($e, $class = __CLASS__, array $params = []) {
		return df_ic($class, __CLASS__, array(self::$P__E => $e) + $params);
	}
}