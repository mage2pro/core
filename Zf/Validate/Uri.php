<?php
namespace Df\Zf\Validate;
final class Uri extends Type {
	/**
	 * @override     
	 * @see \Zend_Validate_Interface::isValid()
	 * @param string|integer $value
	 * @return boolean
	 */
	function isValid($value) {
		$this->prepareValidation($value);
		return \Zend_Uri::check($value);
	}

	/**
	 * @override
	 * @see \Df\Zf\Validate\Type::expected()
	 * @used-by \Df\Zf\Validate\Type::_message()
	 * @return string
	 */
	protected function expected() {return 'an URL';}

	/** @return self */
	static function s() {static $r; return $r ? $r : $r = new self;}
}