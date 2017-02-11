<?php
namespace Df\Zf\Validate;
final class Between extends \Zend_Validate_Between {
	/**             
	 * 2017-01-14
	 * @used-by \Df\Qa\Method::assertParamIsBetween()
	 * @used-by \Df\Qa\Method::assertResultIsBetween()
	 * @used-by \Df\Qa\Method::assertValueIsBetween()
	 * @param int|float|null $min
	 * @param int|float|null $max [optional]
	 * @param bool $inclusive [optional]
	 * @return self
	 */
	static function i($min, $max = null, $inclusive = true) {return new self(
		is_null($min) ? PHP_INT_MIN : $min, is_null($max) ? PHP_INT_MAX : $max, $inclusive
	);}
}