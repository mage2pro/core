<?php
namespace Df\Sso\Source\Button\Type;
/**
 * 2016-11-23
 * Не предоставляющие собственный дизайн для кнопок сервисы (например, «Blackbaud NetCommunity»)
 * используют этот класс, а предоставляющие — класс @see UNL.
 */
class UL extends \Df\Config\Source {
	/**
	 * 2016-11-23
	 * @override
	 * @see \Df\Config\Source::map()
	 * @used-by \Df\Config\Source::toOptionArray()
	 * @see \Df\Sso\Settings\Button::type()
	 * @return array(string => string)
	 */
	protected function map() {return [self::$U => 'unified button', self::$L => 'link'];}

	/**
	 * 2016-11-30
	 * @used-by \Df\Sso\Button::loggedOut()
	 * @param string $type
	 * @return bool
	 */
	static function isLink($type) {return self::$L === $type;}

	/**
	 * 2016-11-30
	 * @used-by \Df\Sso\Button::loggedOut()
	 * @param string $type
	 * @return bool
	 */
	static function isUnified($type) {return self::$U === $type;}

	/** 2016-11-30 */
	private static $L = 'L';

	/** 2016-11-30 */
	private static $U = 'U';
}