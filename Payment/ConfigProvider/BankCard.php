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
	protected function config() {/** @var S $s */ $s = $this->s(); return [
		'prefill' => $s->prefill(), 'requireCardholder' => $s->requireCardholder()
	] + parent::config();}

	/**
	 * 2016-11-10
	 * 2017-02-07
	 * I intentionally do not use the PHP «final» keyword here,
	 * so descendant classes can refine the method's return type using PHPDoc.
	 * @final
	 * @override
	 * @see \Df\Payment\ConfigProvider::s()
	 * @return S
	 */
	protected function s() {return dfc($this, function() {return df_ar(parent::s(), S::class);});}
}