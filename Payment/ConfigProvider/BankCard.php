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
}