<?php
namespace Df\StripeClone;
use Df\StripeClone\CardFormatter as CF;
use Df\StripeClone\Facade\Customer as FCustomer;
use Df\StripeClone\Facade\ICard;
use Df\StripeClone\Settings as S;
/**
 * 2016-11-12
 * @see \Dfe\Spryng\ConfigProvider
 * @see \Dfe\Stripe\ConfigProvider
 * @see \Dfe\Paymill\ConfigProvider
 * @see \Dfe\TwoCheckout\ConfigProvider
 * 2017-03-03
 * The class is not abstract anymore: you can use it as a base for a virtual type.
 * 1) Checkout.com:
 * https://github.com/mage2pro/checkout.com/blob/1.1.32/etc/frontend/di.xml?ts=4#L20-L24
 * https://github.com/mage2pro/checkout.com/blob/1.1.32/etc/frontend/di.xml?ts=4#L9
 *
 * 2) iyzico:
 * https://github.com/mage2pro/iyzico/blob/0.1.8/etc/frontend/di.xml?ts=4#L20-L24
 * https://github.com/mage2pro/iyzico/blob/0.1.8/etc/frontend/di.xml?ts=4#L9
 *
 * 3) Omise:
 * https://github.com/mage2pro/omise/blob/1.5.8/etc/frontend/di.xml?ts=4#L20-L22
 * https://github.com/mage2pro/omise/blob/1.5.8/etc/frontend/di.xml?ts=4#L9
 *
 * 4) Square:
 * https://github.com/mage2pro/square/blob/1.0.25/etc/frontend/di.xml?ts=4#L20-L22
 * https://github.com/mage2pro/square/blob/1.0.25/etc/frontend/di.xml?ts=4#L9
 *
 * @method Method m()
 */
class ConfigProvider extends \Df\Payment\ConfigProvider\BankCard {
	/**
	 * 2016-11-12
	 * @override
	 * @see \Df\Payment\ConfigProvider::config()
	 * @used-by \Df\Payment\ConfigProvider::getConfig()
	 * @see \Dfe\Paymill\ConfigProvider::config()
	 * @see \Dfe\Stripe\ConfigProvider::config()
	 * @see \Dfe\TwoCheckout\ConfigProvider::config()
	 * @return array(string => mixed)
	 */
	protected function config() {return [
		'publicKey' => $this->s()->publicKey(), 'cards' => $this->cards()
	] + parent::config();}

	/**
	 * 2017-02-09
	 * @used-by config()
	 * @return array(string => string)
	 */
	private function cards() {
		/** @var array(string => string) $result */
		$result = [];
		/** @var string|null $customerId */
		if ($customerId = df_ci_get($this)) {
			$this->s()->init();
			/** @var FCustomer $fc */
			$fc = FCustomer::s($this->m());
			/** @var object|null $customer */
			if ($customer = $fc->get($customerId)) {
				$result = array_map(function(ICard $c) {return [
					'id' => $c->id(), 'label' => (new CF($c))->label()
				];}, $fc->cards($customer));
			}
			else {
				// 2017-02-24
				// We can get here, for example, if the store's administrator has switched
				// his Stripe account in the extension's settings: https://mage2.pro/t/3337
				df_ci_save($this, null);
			}
		}
		return $result;
	}
}