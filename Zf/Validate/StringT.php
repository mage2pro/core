<?php
namespace Df\Zf\Validate;
use Magento\Framework\Phrase;
final class StringT extends Type implements \Zend_Filter_Interface {
	/**
	 * @override
	 * @param mixed $v
	 * @throws \Zend_Filter_Exception
	 * @return string|mixed
	 */
	function filter($v) {return is_null($v) || is_int($v) ? strval($v) : $v;}

	/**
	 * @override
	 * @see \Zend_Validate_Interface::isValid()
	 * @param mixed $v
	 * @return bool
	 */
	function isValid($v) {
		$this->v($v);
		# 2015-02-16
		# Раньше здесь стояло просто `is_string($value)`
		# Однако интерпретатор PHP способен неявно и вполне однозначно
		# (без двусмысленностей, как, скажем, с вещественными числами)
		# конвертировать целые числа и null в строки,
		# поэтому пусть целые числа и null всегда проходят валидацию как строки.
		# 2016-07-01 Добавил `|| $value instanceof Phrase`
		# 2017-01-13 Добавил `|| is_bool($value)`
		return is_string($v) || is_int($v) || is_null($v) || is_bool($v) || $v instanceof Phrase;
	}

	/**
	 * @override
	 * @see \Df\Zf\Validate\Type::expected()
	 * @used-by \Df\Zf\Validate\Type::_message()
	 * @return string
	 */
	protected function expected() {return 'a string';}

	/**
	 * @used-by df_check_s()
	 * @used-by \Df\Zf\Validate\StringT::isValid()
	 */
	static function s():self {static $r; return $r ? $r : $r = new self;}
}