<?php
namespace Df\StripeClone;
class Settings extends \Df\Payment\Settings\BankCard {
	/**
	 * 2016-11-12
	 * @see \Dfe\Square\Settings::publicKey()
	 * @return string
	 */
	public function publicKey() {return $this->testable(
		'publishableKey', null, function() {return $this->testable('publicKey');}
	);}
}