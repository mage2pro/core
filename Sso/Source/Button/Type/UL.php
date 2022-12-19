<?php
namespace Df\Sso\Source\Button\Type;
/**
 * 2016-11-23
 * Не предоставляющие собственный дизайн для кнопок сервисы (например, «Blackbaud NetCommunity»)
 * используют этот класс, а предоставляющие — класс @see UNL.
 * @see \Df\Sso\Source\Button\Type\UNL
 */
class UL extends \Df\Config\Source {
	/**
	 * 2016-11-23
	 * @override
	 * @see \Df\Config\Source::map()
	 * @used-by \Df\Config\Source::toOptionArray()
	 * @see \Df\Sso\Source\Button\Type\UNL::map()
	 * @return array(string => string)
	 */
	protected function map():array {return [self::$U => 'unified button', self::$L => 'link'];}

	/**
	 * 2016-11-30
	 * @used-by \Df\Sso\Button::loggedOut()
	 */
	final static function isLink(string $t):bool {return self::$L === $t;}

	/**
	 * 2016-11-30
	 * @used-by \Df\Sso\Button::loggedOut()
	 * @param string $t
	 */
	final static function isUnified($t):bool {return self::$U === $t;}

	/**
	 * 2016-11-30
	 * @used-by self::isLink()
	 * @used-by self::map()
	 */
	private static $L = 'L';

	/**
	 * 2016-11-30
	 * @used-by self::isUnified()
	 * @used-by self::map()
	 */
	private static $U = 'U';
}