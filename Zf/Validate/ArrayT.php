<?php
namespace Df\Zf\Validate;
final class ArrayT extends Type implements \Zend_Filter_Interface {
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
	 * @return bool
	 */
	function isValid($v) {$this->setValue($v); return is_array($v);}

	/**
	 * @override
	 * @see \Df\Zf\Validate\Type::expected()
	 * @used-by \Df\Zf\Validate\Type::_message()
	 * @return string
	 */
	protected function expected() {return 'an array';}

	/**
	 * @used-by \Df\Qa\Method::assertResultIsArray()
	 * @used-by \Df\Qa\Method::assertValueIsArray()
	 */
	static function s():self {static $r; return $r ? $r : $r = new self;}
}