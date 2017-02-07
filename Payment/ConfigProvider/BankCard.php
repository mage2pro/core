<?php
namespace Df\Payment\ConfigProvider;
use Df\Payment\Settings\BankCard as S;
/**
 * 2016-08-22
 * @see \Df\StripeClone\ConfigProvider
 * @see \Dfe\SecurePay\ConfigProvider
 */
class BankCard extends \Df\Payment\ConfigProvider {
	/**
	 * 2016-08-22
	 * @override
	 * @see \Df\Payment\ConfigProvider::config()
	 * @used-by \Df\Payment\ConfigProvider::getConfig()
	 * @see \Df\StripeClone\ConfigProvider::config()
	 * @see \Dfe\SecurePay\ConfigProvider::config()
	 * @return array(string => mixed)
	 */
	protected function config() {return [
		'prefill' => $this->s()->prefill(), 'savedCards' => $this->savedCards()
	] + parent::config();}

	/**
	 * 2016-11-10
	 * 2017-02-07
	 * Не помечаем метод как final, чтобы потомки могли уточнять его результат посредством PHPDoc.
	 * @override
	 * @see \Df\Payment\ConfigProvider::s()
	 * @return S
	 */
	protected function s() {return dfc($this, function() {return df_ar(parent::s(), S::class);});}

	/**
	 * 2016-08-22
	 * @used-by \Df\Payment\ConfigProvider\BankCard::config()
	 * @see \Dfe\Omise\ConfigProvider::savedCards()
	 * @see \Dfe\Stripe\ConfigProvider::savedCards()
	 * @return array(string => string)
	 */
	protected function savedCards() {return [];}
}