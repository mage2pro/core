<?php
namespace Df\Typography;
# 2015-12-16
/** @used-by \Df\Typography\Font::size() */
final class Size extends \Df\Core\O {
	/**
	 * 2015-12-16
	 * 2022-11-15 https://3v4l.org/FGH9K
	 * @used-by \Df\Typography\Font::css():
	 * 		$css->rule('font-size', $this->size());
	 * @used-by \Df\Typography\Font::letter_spacing():
	 */
	function __toString():string {return $this->value() . $this['units'];}

	/**
	 * 2015-12-16
	 * @used-by self::__toString()
	 * @used-by \Df\Typography\Font::css()
	 */
	function value():string {return $this['value'];}
}