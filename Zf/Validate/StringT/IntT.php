<?php
namespace Df\Zf\Validate\StringT;
class IntT extends \Df\Zf\Validate\Type {
	/**
	 * @override
	 * @param string $value
	 * @return bool
	 */
	public function isValid($value) {
		$this->prepareValidation($value);
		/**
		 * Думаю, правильно будет конвертировать строки типа «09» в целые числа без сбоев.
		 * Обратите внимание, что
		 * 9 === (int)'09'.
		 *
		 * Обратите также внимание, что если строка равна '0',
		 * то нам применять @see ltrim нельзя, потому что иначе получим пустую строку.
		 *
		 * 2015-01-23
		 * Раньше код был таким:
				if ('0' !== $value) {
					$value = ltrim($value, '0');
				}
				return strval($value) === strval(intval($value));
			это приводило к неправильной работе метода для значения «0.0» (вещественное число),
		 * потому что ltrim(0.0, '0') возвращает пустую строку.
		 * Предыдущая версия кода была написала 2014-08-30
		 * (хотя и версии до неё были тоже дефектными, просто там дефекты были другие).
		 */
		/** @var string $valueAsString */
		$valueAsString =
			is_string($value) && ('0' !== $value) && !df_starts_with($value, '0.')
			? ltrim($value, '0')
			: strval($value)
		;
		return $valueAsString === strval((int)$value);
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
	public static function s() {static $r; return $r ? $r : $r = new self;}
}