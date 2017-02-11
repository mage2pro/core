<?php
namespace Df\Zf\Validate;
use Magento\Framework\Phrase;
class StringT extends Type implements \Zend_Filter_Interface {
	/**
	 * @override
	 * @param mixed $value
	 * @throws \Zend_Filter_Exception
	 * @return string|mixed
	 */
	function filter($value) {
		return is_null($value) || is_int($value) ? strval($value) : $value;
	}

	/**
	 * @override
	 * @see \Zend_Validate_Interface::isValid()
	 * @param mixed $value
	 * @return bool
	 */
	function isValid($value) {
		$this->prepareValidation($value);
		/**
		 * 2015-02-16
		 * Раньше здесь стояло просто is_string($value)
		 * Однако интерпретатор PHP способен неявно и вполне однозначно
		 * (без двусмысленностей, как, скажем, с вещественными числами)
		 * конвертировать целые числа и null в строки,
		 * поэтому пусть целые числа и null всегда проходят валидацию как строки.
		 *
		 * 2016-07-01
		 * Добавил «|| $value instanceof Phrase»
		 *
		 * 2017-01-13
		 * Добавил «|| is_bool($value)»
		 */
		return is_string($value) || is_int($value) || is_null($value)
			|| is_bool($value) || $value instanceof Phrase
		;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInAccusativeCase() {return 'строку';}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInGenitiveCase() {return 'строки';}

	/** @return self */
	static function s() {static $r; return $r ? $r : $r = new self;}
}