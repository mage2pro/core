<?php
namespace Df\Config\Source;
class NoWhiteBlack extends \Df\Config\SourceT {
	/**
	 * 2016-03-08
	 * @override
	 * @see \Df\Config\Source::map()
	 * @used-by \Df\Config\Source::toOptionArray()
	 * @return array(string => string)
	 */
	protected function map() {return [0 => 'No'] + $this->titles();}

	/**
	 * 2016-05-13
	 * @used-by \Df\Config\Source\NoWhiteBlack::map()
	 * @return string[]
	 */
	protected function titles() {return [self::WHITELIST => 'Whitelist', self::BLACKLIST => 'Blacklist'];}

	const BLACKLIST = 'blacklist';
	const WHITELIST = 'whitelist';
}