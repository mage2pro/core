<?php
namespace Df\Zf\Validate;
final class Nat extends IntT {
	/**
	 * @override
	 * @param  mixed $v
	 * @throws \Zend_Filter_Exception
	 * @return int
	 */
	function filter($v) {/** @var int $r */
		try {$r = df_nat($v);}
		catch (\Exception $e) {df_error(new \Zend_Filter_Exception(df_ets($e)));}
		return $r;
	}

	/**
	 * @override      
	 * @see \Zend_Validate_Interface::isValid()
	 * @param string|integer $v
	 * @return boolean
	 */
	function isValid($v) {return parent::isValid($v) && 0 < $v;}

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
	static function s() {static $r; return $r ? $r : $r = new self;}
}