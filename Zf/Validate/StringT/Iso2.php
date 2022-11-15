<?php
namespace Df\Zf\Validate\StringT;
final class Iso2 extends \Df\Zf\Validate\Type {
	/**
	 * @override
	 * @see \Zend_Validate_Interface::isValid()
	 * @used-by df_check_iso2()
	 * @used-by \Df\Qa\Method::vp()
	 * @used-by \Df\Qa\Method::vv()
	 * @param mixed $v
	 * @return bool
	 */
	function isValid($v) {$this->v($v); return is_string($v) && (2 === mb_strlen($v));}

	/**
	 * @override
	 * @see \Df\Zf\Validate\Type::expected()
	 * @used-by \Df\Zf\Validate\Type::_message()
	 * @return string
	 */
	protected function expected() {return 'an ISO 3166-1 alpha-2 country code';}

	/**
	 * @used-by df_check_iso2()
	 * @used-by \Df\Qa\Method::assertParamIsIso2()
	 */
	static function s():self {static $r; return $r ? $r : $r = new self;}
}