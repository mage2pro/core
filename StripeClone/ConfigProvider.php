<?php
namespace Df\StripeClone;
use Df\StripeClone\Facade\Customer as FCustomer;
use Df\StripeClone\Settings as S;
/**
 * 2016-11-12                      
 * @see \Dfe\CheckoutCom\ConfigProvider
 * @see \Dfe\Iyzico\ConfigProvider
 * @see \Dfe\Omise\ConfigProvider
 * @see \Dfe\Square\ConfigProvider
 * @see \Dfe\Stripe\ConfigProvider
 * @see \Dfe\Paymill\ConfigProvider
 * @see \Dfe\TwoCheckout\ConfigProvider
 * @method Method m()
 */
abstract class ConfigProvider extends \Df\Payment\ConfigProvider\BankCard {
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
	 * Намеренно не ставим final, чтобы потомки могли уточнить тип результата посредством PHPDoc.
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
				$result = $fc->cards($customer);
			}
			else {
				df_ci_save($this, null);
			}
		}
		return $result;
	}
}