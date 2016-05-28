<?php
namespace Df\Zf\Validate;
class FloatT extends Type implements \Zend_Filter_Interface {
	/**
	 * @override
	 * @param mixed $value
	 * @throws \Zend_Filter_Exception
	 * @return float
	 */
	public function filter($value) {
		/** @var float $result */
		try {
			$result = df_float($value);
		}
		catch (\Exception $e) {
			df_error(new \Zend_Filter_Exception(df_ets($e)));
		}
		return $result;
	}

	/**
	 * @override
	 * @param string $value
	 * @return bool
	 */
	public function isValid($value) {
		$this->prepareValidation($value);
		/**
		 * Обратите внимание, что строки не проходят валидацию,
		 * однако мы реализуем интерфейс @see Zend_Filter_Interface
		 * (@see \Df\Zf\Validate\FloatT::filter()),
		 * чтобы пользователь данного класса, имеющий строку (число в виде строки),
		 * мог предварительно сконвертировать её вещественное число
		 * посредством вызова метода @see \Df\Zf\Validate\FloatT::filter().
		 * Так поступает, например, класс @see Df_Core_Model:
		 * при инициализации конкретного свойства данного класса
		 * при наличии фильтра для данного свойства вызывается метод
		 * @see Zend_Filter_Interface::filter().
		 */
		return is_int($value) || is_float($value);
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

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}