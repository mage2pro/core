<?php
namespace Df\Config\Source;
/**
 * 2017-02-05
 * @see \Df\Config\Source\NoWhiteBlack\Specified
 */
class NoWhiteBlack extends \Df\Config\Source {
	/**
	 * 2016-03-08
	 * @override
	 * @see \Df\Config\Source::map()
	 * @used-by \Df\Config\Source::toOptionArray()
	 * @return array(string => string)
	 */
	final protected function map():array {return [0 => 'No'] + $this->titles();}

	/**
	 * 2016-05-13
	 * @used-by self::map()
	 * @see \Df\Config\Source\NoWhiteBlack\Specified::titles()
	 * @return string[]
	 */
	protected function titles():array {return [self::$W => 'Whitelist', self::$B => 'Blacklist'];}

	/**
	 * @used-by self::is()
	 * @used-by self::isNegative()
	 * @used-by self::titles()
	 * @used-by \Df\Config\Source\NoWhiteBlack\Specified::titles()
	 * @var string
	 */
	protected static $B = 'blacklist';
	/**
	 * @used-by self::titles()
	 * @used-by \Df\Config\Source\NoWhiteBlack\Specified::titles()
	 * @var string
	 */
	protected static $W = 'whitelist';

	/**
	 * 2016-05-13
	 * 2016-06-09
	 * Если опция не задана, но метод возвращает «да».
	 * Если опция задана, то смотрим уже тип ограничения: белый или чёрный список.
	 * @used-by \Df\Payment\Method::canUseForCountry()
	 * @used-by \Df\Config\Settings::nwb()
	 * @param string|bool $listType
	 * @param string[] $set
	 */
	final static function is($listType, string $element, array $set):bool {return
		!$listType || (self::$B === $listType xor in_array($element, $set))
	;}

	/**
	 * 2016-06-09
	 * Если опция не задана, но метод возвращает «нет».
	 * Если опция задана, то смотрим уже тип ограничения: белый или чёрный список.
	 * @used-by \Df\Config\Settings::nwbn()
	 * @param string|bool $listType
	 * @param string|null $element
	 * @param string[] $set
	 */
	final static function isNegative($listType, $element, array $set):bool {return
		$listType && (self::$B === $listType xor in_array($element, $set))
	;}
}