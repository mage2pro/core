<?php
namespace Df\Payment\Settings;
/**
 * 2017-02-15
 * @see \Df\StripeClone\Settings
 * @see \Dfe\SecurePay\Settings
 */
abstract class BankCard extends \Df\Payment\Settings {
	/**
	 * 2016-03-14
	 * 2017-02-18
	 * «Dynamic statement descripor»
	 * https://mage2.pro/tags/dynamic-statement-descriptor
	 * https://stripe.com/blog/dynamic-descriptors
	 * https://support.stripe.com/questions/does-stripe-support-dynamic-descriptors
	 * @return string
	 */
	final function dsd() {return $this->v(null, null, function() {return $this->v('statement');});}

	/**
	 * 2016-11-10
	 * «Prefill the Payment Form with Test Data?» 
	 * @used-by \Df\Payment\ConfigProvider\BankCard::config()
	 * @see \Dfe\CheckoutCom\Settings::prefill()
	 * @see \Dfe\Paymill\Settings::prefill()
	 * @return string|false|null|array(string => string)
	 */
	function prefill() {return $this->bv();}

	/**
	 * 2017-02-16
	 * «Require the cardholder's name?» https://mage2.pro/t/2776
	 * @return bool
	 */
	final function requireCardholder() {return $this->b();}
}