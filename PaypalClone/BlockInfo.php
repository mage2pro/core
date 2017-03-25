<?php
namespace Df\PaypalClone;
use Df\Payment\W\Event;
/**
 * 2016-08-29
 * @method Method m()
 * @see \Dfe\AllPay\Block\Info
 * @see \Dfe\SecurePay\Block\Info
 */
abstract class BlockInfo extends \Df\Payment\Block\Info {
	/**
	 * 2016-11-17
	 * @override
	 * @see \Df\Payment\Block\Info::confirmed()
	 * @used-by \Df\Payment\Block\Info::_prepareSpecificInformation()
	 * @return bool
	 */
	final protected function confirmed() {return parent::confirmed() && $this->e();}

	/**
	 * 2016-07-18     
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by confirmed()
	 * @used-by \Dfe\AllPay\Block\Info::prepare()
	 * @used-by \Dfe\AllPay\Block\Info\BankCard::allpayAuthCode()
	 * @used-by \Dfe\AllPay\Block\Info\BankCard::custom()
	 * @used-by \Dfe\AllPay\Block\Info\BankCard::eci()
	 * @used-by \Dfe\AllPay\Block\Info\BankCard::prepareDic()
	 * @used-by \Dfe\AllPay\Block\Info\Offline::custom()
	 * @used-by \Dfe\SecurePay\Block\Info::prepare()
	 * @param string[] ...$k
	 * @return Event|string|null
	 */
	protected function e(...$k) {return df_tmf($this->m(), ...$k);}
}