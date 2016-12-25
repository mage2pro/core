<?php
// 2016-11-12
namespace Df\StripeClone;
use Df\StripeClone\Settings as S;
class ConfigProvider extends \Df\Payment\ConfigProvider\BankCard {
	/**
	 * 2016-11-12
	 * @override
	 * @see \Df\Payment\ConfigProvider::config()
	 * @used-by \Df\Payment\ConfigProvider::getConfig()
	 * @return array(string => mixed)
	 */
	protected function config() {return ['publicKey' => $this->s()->publicKey()] + parent::config();}

	/**
	 * 2016-11-12
	 * @override
	 * @see \Df\Payment\ConfigProvider::s()
	 * @return S
	 */
	protected function s() {return dfc($this, function() {return df_ar(parent::s(), S::class);});}
}