<?php
// 2016-12-26
namespace Df\StripeClone;
abstract class Webhook extends \Df\Payment\Webhook {
	/**
	 * 2017-01-04
	 * @used-by \Df\StripeClone\WebhookF::i()
	 * @param string $v
	 * @return void
	 */
	final public function typeSet($v) {$this->_type = $v;}

	/**
	 * 2016-12-26
	 * @override
	 * @see \Df\Payment\Webhook::config()
	 * @used-by \Df\Payment\Webhook::configCached()
	 * @return array(string => mixed)
	 */
	protected function config() {return [self::$externalIdKey => 'id'];}

	/**
	 * 2017-01-04
	 * @override
	 * @see \Df\Payment\Webhook::testDataFile()
	 * @used-by \Df\Payment\Webhook::testData()
	 * @return string
	 */
	final protected function testDataFile() {return $this->type();}

	/**
	 * 2017-01-04
	 * @override
	 * @see \Df\PaypalClone\Confirmation::type()
	 * @used-by \Df\Payment\Webhook::typeLabel()
	 * @used-by \Dfe\AllPay\Webhook::classSuffix()
	 * @used-by \Dfe\AllPay\Webhook::typeLabel()
	 * @return string
	 */
	final protected function type() {return $this->_type;}

	/**
	 * 2017-01-04
	 * @var string
	 */
	private $_type;
}