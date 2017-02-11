<?php
namespace Df\Zf\Validate;
class Uri extends Type {
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
	 * @return string
	 */
	protected function getExpectedTypeInAccusativeCase() {return 'веб-адрес';}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInGenitiveCase() {return 'веб-адреса';}

	/** @return self */
	static function s() {static $r; return $r ? $r : $r = new self;}
}