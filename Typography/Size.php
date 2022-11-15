<?php
namespace Df\Typography;
/** @used-by \Df\Typography\Font::size() */
final class Size extends \Df\Core\O {
	/**
	 * 2015-12-16
	 * 2022-11-15 https://3v4l.org/FGH9K
	 * @used-by \Df\Typography\Font::css():
	 * 		$css->rule('font-size', $this->size());
	 */
	function __toString():string {return "{$this->value()}{$this->units()}";}

	/** @return string */
	function units() {return $this[self::$P__UNITS];}

	/** @return string */
	function value() {return $this[self::$P__VALUE];}

	/**
	 * 2015-12-16
	 * @return float
	 */
	function valueF() {return df_float($this->value());}

	/**
	 * 2015-12-16
	 * @return int
	 */
	function valueI() {return intval($this->value());}

	/** @var string */
	private static $P__UNITS = 'units';
	/** @var string */
	private static $P__VALUE = 'value';
}