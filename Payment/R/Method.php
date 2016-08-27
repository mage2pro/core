<?php
// 2016-08-27
namespace Df\Payment\R;
use Df\Payment\PlaceOrder;
abstract class Method extends \Df\Payment\Method {
	/**
	 * 2016-08-27
	 * @used-by \Df\Payment\R\Method::getConfigPaymentAction()
	 * @return string
	 */
	abstract protected function redirectUrl();

	/**
	 * 2016-08-27
	 * Первый параметр — для test, второй — для live.
	 * @used-by \Df\Payment\R\Method::getConfigPaymentAction()
	 * @return string[]
	 */
	abstract protected function stageNames();

	/**
	 * @override
	 * @see \Df\Payment\Method::getConfigPaymentAction()
	 * @return string
	 *
	 * 2016-08-27
	 * Сюда мы попадаем только из метода @used-by \Magento\Sales\Model\Order\Payment::place()
	 * причём там наш метод вызывается сразу из двух мест и по-разному.
	 * Умышленно возвращаем null.
	 * @used-by \Magento\Sales\Model\Order\Payment::place()
	 * https://github.com/magento/magento2/blob/ffea3cd/app/code/Magento/Sales/Model/Order/Payment.php#L334-L355
	 */
	final public function getConfigPaymentAction() {
		/** @var array(string => mixed) $p */
		$p = Charge::p($this);
		/** @var string $url */
		$url = $this->url($this->redirectUrl());
		/**
		 * 2016-07-01
		 * К сожалению, если передавать в качестве результата ассоциативный массив,
		 * то его ключи почему-то теряются. Поэтому запаковываем массив в JSON.
		 */
		$this->iiaSet(PlaceOrder::DATA, df_json_encode(['params' => $p, 'uri' => $url]));
		// 2016-05-06
		// Письмо-оповещение о заказе здесь ещё не должно отправляться.
		// «How is a confirmation email sent on an order placement?» https://mage2.pro/t/1542
		$this->o()->setCanSendNewEmailFlag(false);
		// 2016-07-10
		// Сохраняем информацию о транзакции.
		$this->saveRequest($this->transId(), $url, $p);
		return null;
	}

	/**
	 * 2016-08-27
	 * @used-by \Df\Payment\R\Method::getConfigPaymentAction()
	 * @used-by \Dfe\AllPay\Block\Info\BankCard::allpayAuthCode()
	 * @param string $url
	 * @param bool $test [optional]
	 * @param mixed[] ...$params [optional]
	 * @return string
	 */
	final public function url($url, $test = null, ...$params) {
		$test = !is_null($test) ? $test : $this->s()->test();
		/** @var string $stage */
		$stage = call_user_func($test ? 'df_first' : 'df_last', $this->stageNames());
		return vsprintf(str_replace('{stage}', $stage, $url), $params);
	}

	/**
	 * 2016-08-27
	 * @override
	 * @see \Df\Payment\R\Method::stageNames()
	 * @used-by \Df\Payment\R\Method::getConfigPaymentAction()
	 * @return string
	 */
	protected function transId() {return $this->o()->getIncrementId();}
}