<?php
namespace Df\Config\Source;
use Df\Api\Google\Font;
use Df\Api\Google\Fonts;
class GoogleFont extends \Df\Config\Source {
	/**
	 * 2015-11-14
	 * @override
	 * @see \Df\Config\Source::map()
	 * @used-by \Df\Config\Source::toOptionArray()
	 * @return array(string => string)
	 */
	protected function map() {
		/** @var string[] $names */
		$names = df_map(function(Font $font) {return $font->family();}, Fonts::s());
		return array_combine($names, $names);
	}

	/** @return \Df\Config\Source\GoogleFont */
	public static function s() {static $r; return $r ? $r : $r = df_o(__CLASS__);}
}