<?php
namespace Df\Zf\Validate;
class ArrayT extends Type {
	/**
	 * @override
	 * @param mixed $value
	 * @return bool
	 */
	public function isValid($value) {
		$this->prepareValidation($value);
		return is_array($value);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInAccusativeCase() {return 'массив';}
	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInGenitiveCase() {return 'массива';}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}