<?php
namespace Df\Payment\Settings;
/**
 * 2017-02-15
 * @see \Df\StripeClone\Settings
 * @see \Dfe\SecurePay\Settings
 */
class BankCard extends \Df\Payment\Settings {
	/**
	 * 2016-11-10
	 * «Prefill the Payment Form with Test Data?» 
	 * @used-by \Df\Payment\ConfigProvider\BankCard::config()
	 * @see \Dfe\CheckoutCom\Settings::prefill()
	 * @see \Dfe\Paymill\Settings::prefill()
	 * @return string|false|null|array(string => string)
	 */
	function prefill() {return $this->bv();}
}