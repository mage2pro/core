<?php
namespace Df\Zf\Validate;
/**
 * 2016-11-04
 * «Boolean» (unlike «Bool») is not a reserved word in PHP 7 nor PHP 5.x
 * https://3v4l.org/OP3MZ
 * https://php.net/manual/reserved.other-reserved-words.php
 */
final class Boolean extends Type implements \Zend_Filter_Interface {
	/**
	 * @override
	 * @param mixed $v
	 * @throws \Zend_Filter_Exception
	 * @return bool
	 */
	function filter($v) {
		/** @var bool $r */
		try {
			$r = df_bool($v);
		}
		catch (\Exception $e) {
			df_error(new \Zend_Filter_Exception($e->getMessage()));
		}
		return $r;
	}

	/**
	 * @override
	 * @see \Zend_Validate_Interface::isValid()
	 * @param mixed $value
	 * @return bool
	 */
	function isValid($value) {
		$this->prepareValidation($value);
		return is_bool($value);
	}

	/**
	 * @override
	 * @see \Df\Zf\Validate\Type::expected()
	 * @used-by \Df\Zf\Validate\Type::_message()
	 * @return string
	 */
	protected function expected() {return 'a boolean';}

	/** @return self */
	static function s() {static $r; return $r ? $r : $r = new self;}
}