<?php
namespace Df\Payment\Settings;
class StripeClone extends BankCard {
	/**
	 * 2016-11-12
	 * @return string
	 */
	public function publicKey() {return
		$this->testable('publishableKey', null, function() {return $this->testable('publicKey');})
	;}
}


