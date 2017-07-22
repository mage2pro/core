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
		'prefill' => $s->prefill()
		// 2017-07-22
		// It implements the feature:
		// `Add a new option «Prefill the cardholder's name from the billing address?»
		// to the payment modules which require (or accept) the cardholder's name`
		// https://github.com/mage2pro/core/issues/14
		,'prefillCardholder' => $s->prefillCardholder()
		,'requireCardholder' => $s->requireCardholder()
	] + parent::config();}
}