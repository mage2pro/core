<?php
namespace Df\Config\Source;
class GoogleFont extends \Df\Config\Source {
	/**
	 * 2015-11-14
	 * @override
	 * @see \Df\Config\Source::map()
	 * @used-by \Df\Config\Source::toOptionArray()
	 * @return array(string => string)
	 */
	protected function map() {
		return [
			'family_1' => 'Font 1'
			,'family_2' => 'Font 2'
			,'family_3' => 'Font 3'
			,'family_4' => 'Font 4'
			,'family_5' => 'Font 5'
		];
	}

	/** @return \Df\Config\Source\GoogleFont */
	public static function s() {static $r; return $r ? $r : $r = df_o(__CLASS__);}
}