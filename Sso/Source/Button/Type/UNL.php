<?php
# 2016-11-23
namespace Df\Sso\Source\Button\Type;
final class UNL extends UL {
	/**
	 * 2016-11-23
	 * @override
	 * @see \Df\Config\Source::map()
	 * @used-by \Df\Config\Source::toOptionArray()
	 * @see \Df\Sso\Settings\Button::type()
	 * @return array(string => string)
	 */
	protected function map():array {return dfa_insert(parent::map(), 1, [self::$N => 'native button']);}

	/**
	 * 2016-11-29
	 * @used-by \Df\Sso\Button::isNative()
	 * @used-by \Dfe\AmazonLogin\Settings\Button::label()
	 */
	static function isNative(string $type):bool {return self::$N === $type;}

	/** 2016-11-29 */
	private static $N = 'N';
}