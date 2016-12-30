<?php
// 2016-12-26
namespace Df\StripeClone;
abstract class Response extends \Df\Payment\Webhook\Response {
	/**
	 * 2016-12-26
	 * @override
	 * @see \Df\Payment\Webhook\Response::config()
	 * @used-by \Df\Payment\Webhook\Response::configCached()
	 * @return array(string => mixed)
	 */
	protected function config() {return [
		self::$externalIdKey => 'id'
		,self::$typeKey => 'key'
	];}

	/**
	 * 2016-12-30
	 * @override
	 * @see \Df\Payment\Webhook\Response::defaultTestCase()
	 * @used-by \Df\Payment\Webhook\Response::testData()
	 * @return string
	 */
	final protected function defaultTestCase() {return 'charge.capture';}

	/**
	 * 2016-12-26
	 * @override
	 * @see \Df\Payment\Webhook\Response::needCapture()
	 * @used-by \Df\Payment\Webhook\Response::handle()
	 * @return bool
	 */
	final protected function needCapture() {return 'charge.capture' === $this->type();}
}