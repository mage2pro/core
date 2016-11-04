<?php
namespace Df\Zf\Validate;
/**
 * 2016-11-04
 * «Boolean» (unlike «Bool») is not a reserved word in PHP 7 nor PHP 5.x
 * https://3v4l.org/OP3MZ
 * http://php.net/manual/en/reserved.other-reserved-words.php
 */
class Boolean extends Type implements \Zend_Filter_Interface {
	/**
	 * @override
	 * @param mixed $value
	 * @throws \Zend_Filter_Exception
	 * @return bool
	 */
	public function filter($value) {
		/** @var bool $result */
		try {
			$result = df_bool($value);
		}
		catch (\Exception $e) {
			df_error(new \Zend_Filter_Exception($e->getMessage()));
		}
		return $result;
	}

	/**
	 * @override
	 * @param mixed $value
	 * @return bool
	 */
	public function isValid($value) {
		$this->prepareValidation($value);
		return is_bool($value);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInAccusativeCase() {
		return 'значение логического типа («да/нет»)';
	}
	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInGenitiveCase() {
		return 'значения логического типа («да/нет»)';
	}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}