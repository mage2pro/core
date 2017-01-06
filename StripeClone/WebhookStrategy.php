<?php
// 2017-01-06
namespace Df\StripeClone;
abstract class WebhookStrategy {
	/**
	 * 2017-01-06
	 * @used-by \Df\StripeClone\Webhook::_handle()
	 * @return void
	 */
	abstract public function handle();

	/**
	 * 2017-01-06
	 * @used-by \Df\StripeClone\Webhook::_handle()
	 * @param Webhook $w
	 */
	final public function __construct(Webhook $w) {$this->_w = $w;}

	/**
	 * 2017-01-06
	 * @return Webhook
	 */
	final protected function w() {return $this->_w;}

	/**
	 * 2017-01-06
	 * @used-by __construct()
	 * @used-by w()
	 * @var Webhook
	 */
	private $_w;
}