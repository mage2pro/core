<?php
namespace Df\Zf\Validate;
final class IntT extends \Df\Zf\Validate {
	/**
	 * @override
	 * @see \Zend_Validate_Interface::isValid()
	 * @used-by \Df\Qa\Method::vp()
	 * @see df_is_int()
	 * @param string|int $v
	 */
	function isValid($v):bool {
		$this->v($v);
		# Обратите внимание, что здесь нужно именно «==», а не «===»: https://php.net/manual/function.is-int.php#35820
		return is_numeric($v) && ($v == (int)$v);
	}

	/**
	 * @override
	 * @see \Df\Zf\Validate::expected()
	 * @used-by \Df\Zf\Validate::message()
	 */
	protected function expected():string {return 'an integer';}

	/** @used-by \Df\Qa\Method::assertParamIsInteger()  */
	static function s():self {static $r; return $r ? $r : $r = new self;}
}