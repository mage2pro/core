<?php
namespace Df\Config\Source;
class BeforeAfter extends \Df\Config\SourceT {
	/**
	 * 2015-12-28
	 * @override
	 * @see \Df\Config\Source::map()
	 * @used-by \Df\Config\Source::toOptionArray()
	 * @return array(string => string)
	 */
	protected function map() {
		/** @var string[] $values */
		$values = ['before', 'after'];
		return array_combine($values, $values);
	}

	/** @return $this */
	public static function s() {static $r; return $r ? $r : $r = df_o(__CLASS__);}
}