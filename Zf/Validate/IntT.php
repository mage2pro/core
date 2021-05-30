<?php
namespace Df\Zf\Validate;
final class IntT extends Type implements \Zend_Filter_Interface {
	/**
	 * @override
	 * @see \Zend_Filter_Interface::filter()
	 * @param mixed $v
	 * @throws \Zend_Filter_Exception
	 * @return int
	 */
	function filter($v) {return df_try(
		function() use($v) {return df_int($v, true);}
		,function(\Exception $e) {df_error(new \Zend_Filter_Exception(df_ets($e)));}
	);}

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
	 * @see \Df\Zf\Validate\Type::getExpectedTypeInAccusativeCase()
	 * @used-by \Df\Zf\Validate\Type::getDiagnosticMessageForNotNull()
	 * @return string
	 */
	protected function getExpectedTypeInAccusativeCase() {return 'целое число';}

	/**
	 * @override
	 * @see \Df\Zf\Validate\Type::getExpectedTypeInGenitiveCase()
	 * @used-by \Df\Zf\Validate\Type::getDiagnosticMessageForNull()
	 * @return string
	 */
	protected function getExpectedTypeInGenitiveCase() {return 'целого числа';}

	/**
	 * @used-by \Df\Qa\Method::assertParamIsInteger()
	 * @used-by \Df\Qa\Method::assertResultIsInteger()
	 * @return self
	 */
	static function s() {static $r; return $r ? $r : $r = new self;}
}