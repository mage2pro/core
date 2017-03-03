<?php
namespace Df\StripeClone;
use Df\StripeClone\CardFormatter as CF;
use Df\StripeClone\Facade\Customer as FCustomer;
use Df\StripeClone\Facade\ICard;
use Df\StripeClone\Settings as S;
/**
 * 2016-11-12
 * @see \Dfe\Omise\ConfigProvider
 * @see \Dfe\Square\ConfigProvider
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
	 * 2016-11-12
	 * 2017-02-05  
	 * 2017-02-26
	 * I intentionally do not use the PHP «final» keyword here,
	 * so descendant classes can refine the method's return type using PHPDoc.
	 * @final
	 * @override
	 * @see \Df\Payment\ConfigProvider::s()
	 * @return S
	 */
	protected function s() {return dfc($this, function() {return df_ar(parent::s(), S::class);});}

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
			$customer = $fc->get($customerId);
			if ($customer) {
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