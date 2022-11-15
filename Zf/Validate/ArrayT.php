<?php
namespace Df\Zf\Validate;
final class ArrayT extends \Df\Zf\Validate implements \Zend_Filter_Interface {
	/**
	 * 2016-08-31
	 * @override
	 * @see \Zend_Filter_Interface::filter()
	 * @param mixed $v
	 * @return array|mixed
	 */
	function filter($v) {return df_eta($v);}

	/**
	 * @override
	 * @see \Zend_Validate_Interface::isValid()
	 * @param mixed $v
	 */
	function isValid($v):bool {$this->v($v); return is_array($v);}

	/**
	 * @override
	 * @see \Df\Zf\Validate::expected()
	 * @used-by \Df\Zf\Validate::message()
	 */
	protected function expected():string {return 'an array';}

	/**
	 * @used-by \Df\Qa\Method::assertResultIsArray()
	 * @used-by \Df\Qa\Method::assertValueIsArray()
	 */
	static function s():self {static $r; return $r ? $r : $r = new self;}
}