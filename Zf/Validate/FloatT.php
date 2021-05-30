<?php
namespace Df\Zf\Validate;
final class FloatT extends Type implements \Zend_Filter_Interface {
	/**
	 * @override
	 * @param mixed $v
	 * @throws \Zend_Filter_Exception
	 * @return float
	 */
	function filter($v) {/** @var float $r */
		try {$r = df_float($v);}
		catch (\Exception $e) {df_error(new \Zend_Filter_Exception(df_ets($e)));}
		return $r;
	}

	/**
	 * @override
	 * @param string $v
	 * @return bool
	 */
	function isValid($v) {
		$this->prepareValidation($v);
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
		return is_int($v) || is_float($v);
	}

	/**
	 * @override
	 * @see \Df\Zf\Validate\Type::expected()
	 * @used-by \Df\Zf\Validate\Type::_message()
	 * @return string
	 */
	protected function expected() {return 'a float';}

	/**
	 * @used-by \Df\Qa\Method::assertParamIsFloat()
	 * @used-by \Df\Qa\Method::assertResultIsFloat()
	 * @used-by \Df\Qa\Method::assertValueIsFloat()
	 * @return self
	 */
	static function s() {static $r; return $r ? $r : $r = new self;}
}