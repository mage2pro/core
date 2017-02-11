<?php
namespace Df\Core;
class Dumper {
	/**
	 * @used-by df_dump()
	 * @param mixed $value
	 * @return string
	 */
	function dump($value) {return
		is_object($value) ? $this->dumpObject($value) :
			(is_array($value) ? $this->dumpArray($value) :
				(is_bool($value) ? df_bts($value) :
					(is_string($value) ? $value : print_r($value, true))
				)
			)
	;}

	/**
	 * @used-by df_print_params()
	 * @used-by dumpArray()
	 * @used-by dumpObject()
	 * @param mixed[]|array(string => mixed) $array
	 * @return string
	 */
	function dumpArrayElements(array $array) {
		// 2015-01-25
		// для удобства сравнения двух версий массива/объекта в Araxis Merge
		ksort($array);
		/** @var string $result */
		$result = '';
		foreach ($array as $key => $value)  {
			/** @var string|int $key */
			/** @var mixed $value */
			$result .= "\n" . '[' . $key . '] => ' . $this->dump($value);
		}
		return $result;
	}

	/**
	 * @param mixed $array
	 * @return string
	 */
	private function dumpArray(array $array) {
		return "array(" . df_tab_multiline($this->dumpArrayElements($array)) . "\n)";
	}

	/**
	 * @param object $object
	 * @return string
	 */
	private function dumpObject($object) {
		/** @var string $hash */
		$hash = spl_object_hash($object);
		/** @var string $result */
		if (isset($this->_dumped[$hash])) {
			$result = sprintf('[рекурсия: %s]', get_class($object));
		}
		else {
			$this->_dumped[$hash] = true;
			$result =
				!$object instanceof \Magento\Framework\DataObject
				? get_class($object)
				: sprintf(
					"%s(%s\n)"
					, get_class($object)
					, df_tab_multiline($this->dumpArrayElements($object->getData()))
				)
			;
		}
		return $result;
	}

	/** @var array(string => bool) */
	private $_dumped = [];

	/**
	 * Обратите внимание, что мы намеренно не используем для @see Df_Core_Dumper
	 * объект-одиночку, потому что нам надо вести учёт выгруженных объектов,
	 * чтобы не попасть в бесконечную рекурсию при циклических ссылках.
	 * @return \Df\Core\Dumper
	 */
	static function i() {return new self;}
}