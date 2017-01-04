<?php
// 2016-12-26
namespace Df\StripeClone;
abstract class Webhook extends \Df\Payment\Webhook {
	/**
	 * 2016-12-26
	 * @override
	 * @see \Df\Payment\Webhook::config()
	 * @used-by \Df\Payment\Webhook::configCached()
	 * @return array(string => mixed)
	 */
	protected function config() {return [self::$externalIdKey => 'id'];}

	/**
	 * 2016-12-30
	 * @override
	 * @see \Df\Payment\Webhook::defaultTestCase()
	 * @used-by \Df\Payment\Webhook::testData()
	 * @return string
	 */
	final protected function defaultTestCase() {return 'charge.capture';}
}