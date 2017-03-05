<?php
namespace Df\PaypalClone\Method;
use Df\PaypalClone\Charge;
use Df\Payment\PlaceOrder;
/**
 * 2017-01-22
 * @see \Df\GingerPaymentsBase\Method
 * @see \Dfe\AllPay\Method
 * @see \Dfe\SecurePay\Method
 */
abstract class Normal extends \Df\PaypalClone\Method {
	/**
	 * 2016-08-27
	 * @used-by \Df\PaypalClone\Method::getConfigPaymentAction()
	 * @see \Dfe\AllPay\Method::redirectUrl()
	 * @see \Dfe\SecurePay\Method::redirectUrl()
	 * @return string
	 */
	abstract protected function redirectUrl();

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
	 * Сюда мы попадаем только из метода @used-by \Magento\Sales\Model\Order\Payment::place()
	 * причём там наш метод вызывается сразу из двух мест и по-разному.
	 * Умышленно возвращаем null.
	 * @used-by \Magento\Sales\Model\Order\Payment::place()
	 * https://github.com/magento/magento2/blob/ffea3cd/app/code/Magento/Sales/Model/Order/Payment.php#L334-L355
	 * @override
	 * @see \Df\Payment\Method::getConfigPaymentAction()
	 * @return string
	 */
	final function getConfigPaymentAction() {
		/** @var string $id */
		/** @var array(string => mixed) $p */
		list($id, $p) = Charge::p($this);
		/** @var string $url */
		$url = $this->url($this->redirectUrl());
		/** @var array(string => mixed) $request */
		$request = ['params' => $p, 'uri' => $url];
		/**
		 * 2016-07-01
		 * К сожалению, если передавать в качестве результата ассоциативный массив,
		 * то его ключи почему-то теряются. Поэтому запаковываем массив в JSON.
		 */
		$this->iiaSet(PlaceOrder::DATA, df_json_encode($request));
		// 2016-12-20
		if ($this->s()->log()) {
			dfp_report($this, $request, 'request');
		}
		// 2016-05-06
		// Письмо-оповещение о заказе здесь ещё не должно отправляться.
		// «How is a confirmation email sent on an order placement?» https://mage2.pro/t/1542
		$this->o()->setCanSendNewEmailFlag(false);
		// 2016-07-10
		// Сохраняем информацию о транзакции.
		$this->addTransaction($id, $p);
		return null;
	}

	/**
	 * 2016-08-27
	 * @used-by getConfigPaymentAction()
	 * @used-by \Dfe\AllPay\Block\Info\BankCard::allpayAuthCode()
	 * @param string $url
	 * @param bool $test [optional]
	 * @param mixed[] ...$args [optional]
	 * @return string
	 */
	final function url($url, $test = null, ...$args) {return df_url_staged(
		!is_null($test) ? $test : $this->test(), $url, $this->stageNames(), ...$args
	);}
}