<?php
namespace Df\Zf\Validate\StringT;
final class FloatT extends Parser {
	/**
	 * @override
	 * @see \Zend_Validate_Interface::isValid()
	 * @param string $v
	 * @return bool
	 */
	function isValid($v) {
		$this->prepareValidation($v);
		/**
		 * 1) Избавляет от сбоев типа
		 * «Система не смогла распознать значение «368.» типа «string» как вещественное число.»
		 * http://magento-forum.ru/topic/4648/
		 * Другими словами, думаю, что будет правильным
		 * конвертировать строки типа «368.» в вещественные числа без сбоев.
		 * 2) 368.0 === floatval('368.'), поэтому функция @see df_float()
		 * сконвертирует строку «368.» в вещественное число без проблем.
		 */
		if (is_string($v) && df_ends_with($v, '.') && ('.' !== $v)) {
			$v .= '0';
		}
		return $this->getZendValidator('en_US')->isValid($v) || $this->getZendValidator('ru_RU')->isValid($v);
	}

 	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInAccusativeCase() {return 'вещественное число';}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInGenitiveCase() {return 'вещественного числа';}

	/**
	 * @override
	 * @return string
	 */
	protected function getZendValidatorClass() {return 'Zend_Validate_Float';}

	/** @return self */
	static function s() {static $r; return $r ? $r : $r = new self;}
}