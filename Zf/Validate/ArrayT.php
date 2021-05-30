<?php
namespace Df\Zf\Validate;
final class ArrayT extends Type implements \Zend_Filter_Interface {
	/**
	 * 2016-08-31
	 * @override
	 * @see \Zend_Filter_Interface::filter()
	 * @param mixed $value
	 * @return array|mixed
	 */
	function filter($value) {return df_eta($value);}

	/**
	 * @override
	 * @see \Zend_Validate_Interface::isValid()
	 * @param mixed $value
	 * @return bool
	 */
	function isValid($value) {
		$this->prepareValidation($value);
		return is_array($value);
	}

	/**
	 * @override
	 * @see \Df\Zf\Validate\Type::expected()
	 * @used-by \Df\Zf\Validate\Type::_message()
	 * @return string
	 */
	protected function expected() {return 'an array';}

	/** @return self */
	static function s() {static $r; return $r ? $r : $r = new self;}
}