<?php
namespace Df\Payment\Source;
class CountryRestriction extends \Df\Config\SourceT {
	/**
	 * 2016-03-08
	 * @override
	 * @see \Df\Config\Source::map()
	 * @used-by \Df\Config\Source::toOptionArray()
	 * @return array(string => string)
	 */
	protected function map() {
		return [0 => 'No', self::WHITELIST => 'Whitelist', self::BLACKLIST => 'Blacklist'];
	}

	const BLACKLIST = 'blacklist';
	const WHITELIST = 'whitelist';
}