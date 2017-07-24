<?php
namespace Df\StripeClone;
use Df\StripeClone\CardFormatter as CF;
use Df\StripeClone\Facade\Customer as FCustomer;
use Df\StripeClone\Facade\ICard;
use Df\StripeClone\Settings as S;
/**
 * 2016-11-12
 * @see \Dfe\Moip\ConfigProvider
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
	 * @see \Dfe\Moip\ConfigProvider::config()
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
		$result = []; /** @var array(string => string) $result */
		if ($customerId = df_ci_get($this) /** @var string|null $customerId */) {
			$this->s()->init();
			$fc = FCustomer::s($m = $this->m()); /** @var FCustomer $fc */ /** @var Method $m */
			if ($customer = $fc->get($customerId) /** @var object|null $customer */) {
				$result = array_map(function(ICard $c) use($m) {
					// 2017-07-24
					// Unfortunately, the one-liner fails on PHP 5.6.30:
					// return ['id' => $c->id(), 'label' => (CF::s($m, $c))->label()];
					// «syntax error, unexpected '->' (T_OBJECT_OPERATOR), expecting ']'»
					// https://github.com/mage2pro/core/issues/16
					// See also: https://github.com/mage2pro/core/issues/15
					$cf = CF::s($m, $c); /** @var CF $cf */
					return ['id' => $c->id(), 'label' => $cf->label()];
				}, $fc->cards($customer));
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