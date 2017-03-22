<?php
namespace Df\PaypalClone\Method;
use Df\PaypalClone\Charge;
use Df\Payment\PlaceOrderInternal as PO;
use Magento\Sales\Model\Order\Payment\Transaction as T;
/**
 * 2017-01-22
 * @see \Df\GingerPaymentsBase\Method
 * @see \Dfe\AllPay\Method
 * @see \Dfe\SecurePay\Method
 */
abstract class Normal extends \Df\PaypalClone\Method {
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