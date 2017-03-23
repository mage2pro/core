<?php
namespace Df\PaypalClone;
use Magento\Sales\Model\Order\Payment\Transaction as T;
/**
 * 2017-01-22
 * @see \Dfe\AllPay\Method
 * @see \Dfe\SecurePay\Method
 */
abstract class Method extends \Df\Payment\Method {
	/**
	 * 2016-08-27
	 * Первый параметр — для test, второй — для live.
	 * @used-by url()
	 * @used-by \Df\PaypalClone\Refund::stageNames()
	 * @see \Dfe\AllPay\Method::stageNames()
	 * @see \Dfe\SecurePay\Method::stageNames()
	 * @return string[]
	 */
	abstract function stageNames();

	/**
	 * 2016-08-27
	 * @used-by \Dfe\AllPay\Block\Info\BankCard::allpayAuthCode()
	 * @used-by \Dfe\AllPay\Init\Action::redirectUrl()
	 * @used-by \Dfe\SecurePay\Init\Action::redirectUrl()
	 * @param string $url
	 * @param bool $test [optional]
	 * @param mixed[] ...$args [optional]
	 * @return string
	 */
	final function url($url, $test = null, ...$args) {return df_url_staged(
		!is_null($test) ? $test : $this->test(), $url, $this->stageNames(), ...$args
	);}
}