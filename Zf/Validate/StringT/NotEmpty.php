<?php
namespace Df\Zf\Validate\StringT;
class NotEmpty extends \Df\Zf\Validate\Type {
	/**
	 * @override
	 * @param mixed $value
	 * @return bool
	 */
	public function isValid($value) {
		$this->prepareValidation($value);
		/**
		 * 2015-02-16
		 * Раньше здесь стояло is_string($value) && ('' !== strval($value))
		 * Однако интерпретатор PHP способен неявно и вполне однозначно
		 * (без двусмысленностей, как, скажем, с вещественными числами)
		 * конвертировать целые числа в строки,
		 * поэтому пусть целые числа всегда проходят валидацию как непустые строки.
		 */
		return is_int($value) || (is_string($value) && ('' !== strval($value)));
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInAccusativeCase() {return 'непустую строку';}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInGenitiveCase() {return 'непустой строки';}

	/** @return NotEmpty */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}