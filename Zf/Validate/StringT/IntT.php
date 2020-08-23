<?php
namespace Df\Zf\Validate\StringT;
class IntT extends \Df\Zf\Validate\Type {
	/**
	 * @override
	 * @see \Zend_Validate_Interface::isValid()
	 * @used-by df_int()
	 * @param string $v
	 * @return bool
	 */
	function isValid($v) {
		$this->prepareValidation($v);
		/**
		 * 1) Думаю, правильно будет конвертировать строки типа «09» в целые числа без сбоев.
		 * 2) 9 === (int)'09'.
		 * 3) Если строка равна '0', то нам применять @see ltrim нельзя, потому что иначе получим пустую строку.
		 * 2015-01-23
		 * Раньше код был таким:
		 *		if ('0' !== $v) {
		 *			$v = ltrim($v, '0');
		 *		}
		 *		return strval($v) === strval(intval($v));
		 * Это приводило к неправильной работе метода для значения «0.0» (вещественное число),
		 * потому что ltrim(0.0, '0') возвращает пустую строку.
		 * Предыдущая версия кода была написала 2014-08-30
		 * (хотя и версии до неё были тоже дефектными, просто там дефекты были другие).
		 */
		return strval((int)$v) === (is_string($v) && ('0' !== $v) && !df_starts_with($v, '0.')
			? ltrim($v, '0') : strval($v)
		);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInAccusativeCase() {return 'целое число';}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInGenitiveCase() {return 'целого числа';}

	/** @return self */
	static function s() {static $r; return $r ? $r : $r = new self;}
}