<?php
namespace Df\StripeClone;
use Df\Core\Exception as DFE;
class Settings extends \Df\Payment\Settings\BankCard {
	/**
	 * 2017-02-08
	 * @used-by \Dfe\CheckoutCom\Settings::api()
	 * @used-by \Dfe\Omise\Settings::init()
	 * @used-by \Dfe\Paymill\T\Charge::t01()
	 * @used-by \Dfe\Stripe\Settings::init()
	 * @used-by \Dfe\TwoCheckout\Settings::init()
	 * @return string
	 */
	final function privateKey() {return $this->key('testableP', 'private', 'secret');}

	/**
	 * 2016-11-12
	 * @see \Dfe\Square\Settings::publicKey()
	 * @return string
	 */
	function publicKey() {return $this->key('testable', 'public', 'publishable');}

	/**
	 * 2017-02-08
	 * @used-by privateKey()
	 * @used-by publicKey()
	 * @uses testable()
	 * @uses testableP()
	 * @param string $method
	 * @param $type
	 * @param string $alt
	 * @return string
	 * @throws DFE
	 */
	private function key($method, $type, $alt) {return
		$this->$method("{$type}Key", null, function() use($method, $alt) {return
			$this->$method("{$alt}Key");}
		) ?: df_error("Please set your %s {$type} key in the Magento backend.", dfp_method_title($this))
	;}
}