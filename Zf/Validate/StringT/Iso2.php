<?php
namespace Df\Zf\Validate\StringT;
final class Iso2 extends \Df\Zf\Validate {
	/**
	 * @override
	 * @see \Zend_Validate_Interface::isValid()
	 * @used-by df_check_iso2()
	 * @used-by \Df\Qa\Method::vp()
	 * @param mixed $v
	 */
	function isValid($v):bool {$this->v($v); return is_string($v) && (2 === mb_strlen($v));}

	/**
	 * @override
	 * @see \Df\Zf\Validate::expected()
	 * @used-by \Df\Zf\Validate::message()
	 */
	protected function expected():string {return 'an ISO 3166-1 alpha-2 country code';}

	/**
	 * @used-by df_check_iso2()
	 * @used-by \Df\Qa\Method::assertParamIsIso2()
	 */
	static function s():self {static $r; return $r ? $r : $r = new self;}
}