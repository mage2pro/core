<?php
namespace Df\Zf\Validate;
class ArrayT extends Type implements \Zend_Filter_Interface {
	/**
	 * 2016-08-31
	 * @override
	 * @see \Zend_Filter_Interface::filter()
	 * @param mixed $value
	 * @return array|mixed
	 */
	public function filter($value) {return df_nta($value);}

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