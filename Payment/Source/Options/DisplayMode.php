<?php
namespace Df\Payment\Source\Options;
// 2017-09-21
final class DisplayMode extends \Df\Config\Source {
	/**
	 * 2017-09-21
	 * @override
	 * @see \Df\Config\Source::map()
	 * @used-by \Df\Config\Source::toOptionArray()
	 * @return array(string => string)
	 */
	protected function map() {return [self::IMAGES => 'images', 'text' => 'text'];}

	/**
	 * 2017-09-21
	 * @used-by \Df\Payment\Source\Options\DisplayMode::map()
	 */
	const IMAGES = 'images';
}