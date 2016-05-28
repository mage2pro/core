<?php
namespace Df\Zf\Validate;
class Nat extends IntT {
	/**
	 * @override
	 * @param  mixed $value
	 * @throws \Zend_Filter_Exception
	 * @return int
	 */
	public function filter($value) {
		/** @var int $result */
		try {
			$result = df_nat($value);
		}
		catch (\Exception $e) {
			df_error(new \Zend_Filter_Exception(df_ets($e)));
		}
		return $result;
	}

	/**
	 * @override
	 * @param string|integer $value
	 * @return boolean
	 */
	public function isValid($value) {return parent::isValid($value) && (0 < $value);}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInAccusativeCase() {return 'натуральное число';}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInGenitiveCase() {return 'натурального числа';}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}