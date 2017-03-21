<?php
namespace Df\Payment\Source;
// 2016-03-07
final class ACR extends AC {
	/**
	 * 2016-03-07
	 * @override
	 * @see \Df\Payment\Source\AC::map()
	 * @used-by \Df\Config\Source::toOptionArray()
	 * @return array(string => string)
	 */
	protected function map() {return parent::map() + [self::R => 'Review'];}

	/**
	 * 2017-03-21
	 * @used-by \Df\Payment\Source\ACR::map()
	 * @used-by \Df\StripeClone\Method::isInitializeNeeded()
	 */
	const R = 'review';
}