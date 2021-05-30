<?php
namespace Df\Zf\Validate;
/**
 * @see \Df\Zf\Validate\Nat
 * @see \Df\Zf\Validate\Nat0
 */
abstract class IntT extends Type implements \Zend_Filter_Interface {
	/**
	 * @override
	 * @param mixed $value
	 * @throws \Zend_Filter_Exception
	 * @return int
	 */
	function filter($value) {
		/** @var int $result */
		try {
			$result = df_int($value, $allowNull = true);
		}
		catch (\Exception $e) {
			df_error(new \Zend_Filter_Exception(df_ets($e)));
		}
		return $result;
	}

	/**
	 * @override
	 * @see \Zend_Validate_Interface::isValid()
	 * @see df_is_int()
	 * @param string|int $v
	 * @return boolean
	 */
	function isValid($v) {
		$this->prepareValidation($v);
		# Обратите внимание, что здесь нужно именно «==», а не «===»: http://php.net/manual/function.is-int.php#35820
		return is_numeric($v) && ($v == (int)$v);
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
}