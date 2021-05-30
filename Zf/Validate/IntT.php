<?php
namespace Df\Zf\Validate;
final class IntT extends Type {
	/**
	 * @override
	 * @see \Zend_Validate_Interface::isValid()
	 * @used-by \Df\Qa\Method::vp()
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
	 * @see \Df\Zf\Validate\Type::expected()
	 * @used-by \Df\Zf\Validate\Type::_message()
	 * @return string
	 */
	protected function expected() {return 'an integer';}

	/**
	 * @used-by \Df\Qa\Method::assertParamIsInteger()
	 * @return self
	 */
	static function s() {static $r; return $r ? $r : $r = new self;}
}