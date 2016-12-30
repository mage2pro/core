<?php
namespace Df\StripeClone;
class Settings extends \Df\Payment\Settings\BankCard {
	/**
	 * 2016-12-27
	 * @see \Dfe\TwoCheckout\Settings::init()
	 * @see \Dfe\Omise\Settings::init()
	 * @see \Dfe\Stripe\Settings::init()
	 * @used-by \Df\StripeClone\Method::api()
	 * @return void
	 */
	public function init() {}

	/**
	 * 2016-11-12
	 * @return string
	 */
	public function publicKey() {return
		$this->testable('publishableKey', null, function() {return $this->testable('publicKey');})
	;}
}


