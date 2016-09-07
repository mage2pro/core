<?php
namespace Df\Payment\ConfigProvider;
class BankCard extends \Df\Payment\ConfigProvider {
	/**
	 * 2016-08-22
	 * @override
	 * @see \Df\Payment\ConfigProvider::config()
	 * @used-by \Df\Payment\ConfigProvider::getConfig()
	 * @return array(string => mixed)
	 */
	protected function config() {return ['savedCards' => $this->savedCards()] + parent::config();}

	/**
	 * 2016-08-22
	 * @used-by \Df\Payment\ConfigProvider\BankCard::config()
	 * @return array(string => string)
	 */
	protected function savedCards() {return [];}
}