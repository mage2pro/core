<?php
namespace Df\Config\Source;
/** @method static SizeUnit s() */
class SizeUnit extends \Df\Config\SourceT {
	/**
	 * 2015-12-11
	 * https://developer.mozilla.org/en-US/docs/Web/CSS/length
	 * https://developer.mozilla.org/en-US/docs/Web/CSS/percentage
	 * @override
	 * @see \Df\Config\Source::map()
	 * @used-by \Df\Config\Source::toOptionArray()
	 * @return array(string => string)
	 */
	protected function map() {
		/** @var string[] $values */
		$values = ['rem', 'em', 'px', 'pt', '%'];
		return array_combine($values, $values);
	}
}