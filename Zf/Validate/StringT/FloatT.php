<?php
namespace Df\Zf\Validate\StringT;
class FloatT extends Parser {
	/**
	 * @override
	 * @param string $value
	 * @return bool
	 */
	public function isValid($value) {
		$this->prepareValidation($value);
		/**
		 * Избавляет от сбоев типа
		 * «Система не смогла распознать значение «368.» типа «string» как вещественное число.»
		 * http://magento-forum.ru/topic/4648/
		 * Другими словами, думаю, что будет правильным
		 * конвертировать строки типа «368.» в вещественные числа без сбоев.
		 *
		 * Обратите внимание, что 368.0 === floatval('368.'),
		 * поэтому функция @see df_float()
		 * сконвертирует строку «368.» в вещественное число без проблем.
		 */
		if (is_string($value) && df_ends_with($value, '.') && ('.' !== $value)) {
			$value .= '0';
		}
		return
				$this->getZendValidator('en_US')->isValid($value)
			||
				$this->getZendValidator('ru_RU')->isValid($value)
		;
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
	public static function s() {static $r; return $r ? $r : $r = new self;}
}