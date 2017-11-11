<?php
namespace Df\Payment\Init;
use Df\Payment\Method as M;
use Df\Payment\PlaceOrderInternal as PO;
use Df\Payment\Settings as S;
use Df\Payment\Source\AC;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment\Transaction as T;
/**
 * 2017-03-21 The class is not abstract: it is directly used by some my modules (e.g.: Stripe, Paymill).
 * @see \Df\GingerPaymentsBase\Init\Action
 * @see \Df\PaypalClone\Init\Action
 * @see \Dfe\Omise\Init\Action
 * @see \Dfe\Qiwi\Init\Action
 * @see \Dfe\Stripe\Init\Action
 */
class Action {
	/**
	 * 2017-03-21
	 * @used-by \Df\Payment\Method::getConfigPaymentAction()
	 * @return string|null
	 */
	final function action() {return $this->_m->action(function() {
		$m = $this->_m; /** @var M $m */
		$this->preorder();
		$p = $this->redirectParams(); /** @var array(string => mixed) $p */
		if ($url = dfp_url_api($m, $this->redirectUrl())) { /** @var string|null $url */
			PO::setRedirectData($m, $url, $p, $this->forceGet());
			// 2016-12-20
			if ($this->s()->log()) {
				dfp_report($m, ['Redirect Params' => $p, 'Redirect URL' => $url], 'request');
			}
			// 2016-05-06
			// Postpone sending an order confirmation email to the customer,
			// because the customer should pass 3D Secure validation first.
			// «How is a confirmation email sent on an order placement?» https://mage2.pro/t/1542
			$this->o()->setCanSendNewEmailFlag(false);
		}
		/**
		 * 2017-03-26
		 * Stripe-подобные платёжные модули типа Omise
		 * (и в перспективе, после надлежащего рефакторинга — Checkout.com),
		 * осуществляют проверку 3D Secure с перенаправлением браузера.
		 * Они записывают первичную транзакцию в методе @see \Df\StripeClone\Method::chargeNew()
		 * https://github.com/mage2pro/core/blob/2.4.0/StripeClone/Method.php#L117-L164
		 * В этом случае мы не попадаем в расположенную ниже ветку,
		 * потому что @uses transId() по умолчанию возвращает null,
		 * а указанные модули этот метод намеренно не перекрывают.
		 * 2017-03-29
		 * Транзакция может записываться и без перенаправления:
		 * например, при выборе опции Bank Transfer модуля Kassa Compleet.
		 */
		if ($id = $this->transId() /** @var string|null $id */) {
			$result = null;
			$m->ii()->setTransactionId($id); // 2016-07-10 Сохраняем информацию о транзакции.
			if (!$this->forceGet()) {
				/**
				 * 2017-03-26
				 * Некоторые модули (Ginger Payments, Kassa Compleet, QIWI Wallet) перенаправляют покупателя
				 * на платёжную страницу ПС без параметров POST: они передают параметры через URL.
				 * В этом случае данный вызов @uses \Df\Payment\Method::iiaSetTRR()
				 * безвреден (он ничего не сделает), а параметры запроса сохраняются в транзакции
				 * каким-то другим методом, например:
				 * @see \Df\GingerPaymentsBase\Init\Action::req()
				 * @see \Df\GingerPaymentsBase\Init\Action::res()
				 * @see \Dfe\Qiwi\Init\Action::preorder()
				 */
				$m->iiaSetTRR($p);
			}
			/**
			 * 2016-07-10
			 * @uses \Magento\Sales\Model\Order\Payment\Transaction::TYPE_PAYMENT —
			 * это единственный транзакция без специального назначения,
			 * и поэтому мы можем безопасно его использовать
			 * для сохранения информации о нашем запросе к платёжной системе.
			 */
			$m->ii()->addTransaction(T::TYPE_PAYMENT);
		}
		/**
		 * 2017-11-11
		 * We return `null` in the redirection case.
		 * The result of this method will be returned
		 * by the @used-by \Df\Payment\Method::getConfigPaymentAction() method
		 * to the @used-by \Magento\Sales\Model\Order\Payment::place() method:
		 *		$orderState = Order::STATE_NEW;
		 *		$orderStatus = $methodInstance->getConfigData('order_status');
		 *		<...>
		 *		$action = $methodInstance->getConfigPaymentAction();
		 *		if ($action) {
		 *			if ($methodInstance->isInitializeNeeded()) {
		 *				$stateObject = new \Magento\Framework\DataObject();
		 *				// For method initialization we have to use original config value for payment action
		 *				$methodInstance->initialize(
		 * 					$methodInstance->getConfigData('payment_action'), $stateObject
		 * 				);
		 *				$orderState = $stateObject->getData('state') ?: $orderState;
		 *				$orderStatus = $stateObject->getData('status') ?: $orderStatus;
		 *				$isCustomerNotified = $stateObject->hasData('is_notified')
		 *					? $stateObject->getData('is_notified')
		 *					: $isCustomerNotified;
		 *			} else {
		 *				$orderState = Order::STATE_PROCESSING;
		 *				$this->processAction($action, $order);
		 *				$orderState = $order->getState() ? $order->getState() : $orderState;
		 *				$orderStatus = $order->getStatus() ? $order->getStatus() : $orderStatus;
		 *			}
		 *		} else {
		 *			$order->setState($orderState)
		 *				->setStatus($orderStatus);
		 *		}
		 * This code is the same in Magento 2.0.0 - 2.2.1:
		 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Sales/Model/Order/Payment.php#L316-L343
		 * https://github.com/magento/magento2/blob/2.2.1/app/code/Magento/Sales/Model/Order/Payment.php#L348-L375
		 * As you can see, if $action is `null`,
		 * then the current order will have the @see \Magento\Sales\Model\Order::STATE_NEW state:
		 * it is what we need in the redirection case.
		 */
		return $url || $id ? null : $this->preconfigured();
	}, false);}

	/**
	 * 2017-03-21
	 * 2016-12-24 Сценарий «Review» неосуществим при необходимости проверки 3D Secure,
	 * ведь администратор не в состоянии пройти проверку 3D Secure за покупателя.
	 * 2017-03-21 Поэтому мы обрабатываем случай «Review» точно так же, как и «Authorize».
	 * @used-by \Dfe\AlphaCommerceHub\Charge::pCharge()
	 * @used-by \Dfe\Omise\Init\Action::redirectUrl()
	 * @used-by \Dfe\Stripe\Init\Action::redirectUrl()
	 * @used-by \Dfe\Stripe\W\Event\Source::ttCurrent()
	 * @return bool
	 */
	final function preconfiguredToCapture() {return AC::c($this->preconfigured());}

	/**
	 * 2017-03-26
	 * @used-by \Df\GingerPaymentsBase\Init\Action::transId()
	 * @used-by \Df\PaypalClone\Init\Action::transId()
	 * @used-by \Dfe\Qiwi\Init\Action::transId()
	 * @used-by \Dfe\Stripe\Init\Action::transId()
	 * @param string $id
	 * @param string|null $t [optional]
	 * @return string
	 */
	final protected function e2i($id, $t = null) {return $this->m()->tid()->e2i($id, $t);}

	/**
	 * 2017-09-10
	 * @used-by action()
	 * @see \Dfe\Qiwi\Init\Action::redirectMethod()
	 * @return bool
	 */
	protected function forceGet() {return false;}

	/**
	 * 2017-03-21
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by e2i()
	 * @used-by \Df\GingerPaymentsBase\Init\Action::req()
	 * @used-by \Df\GingerPaymentsBase\Init\Action::res()
	 * @used-by \Df\PaypalClone\Init\Action::charge()
	 * @used-by \Dfe\AlphaCommerceHub\Init\Action::redirectUrl()
	 * @used-by \Dfe\Qiwi\Init\Action::redirectUrl()
	 * @used-by \Dfe\Qiwi\Init\Action::req()
	 * @used-by \Dfe\Qiwi\Init\Action::res()
	 * @used-by \Dfe\Stripe\Init\Action::redirectUrl()
	 * @return M
	 */
	protected function m() {return $this->_m;}

	/**
	 * 2017-03-21
	 * @used-by action()
	 * @used-by preconfigured()
	 * @used-by \Dfe\Stripe\Init\Action::need3DS()
	 * @return O
	 */
	final protected function o() {return $this->_m->o();}

	/**
	 * 2017-03-21
	 * @used-by action()
	 * @see \Df\PaypalClone\Init\Action::redirectParams()
	 * @see \Dfe\Qiwi\Init\Action::redirectParams()
	 * @return array(string => mixed)
	 */
	protected function redirectParams() {return [];}

	/**
	 * 2016-12-24
	 * 2017-01-12
	 * Помимо этого метода имеется также метод @see \Df\StripeClone\Method::redirectNeeded(),
	 * который принимает решение о необходимости проверки 3D Secure
	 * на основании конкретного параметра $charge.
	 * @used-by action()
	 * @see \Df\GingerPaymentsBase\Init\Action::redirectUrl()
	 * @see \Dfe\AllPay\Init\Action::redirectUrl()
	 * @see \Dfe\AlphaCommerceHub\Init\Action::redirectUrl()
	 * @see \Dfe\Dragonpay\Init\Action::redirectUrl()
	 * @see \Dfe\IPay88\Init\Action::redirectUrl()
	 * @see \Dfe\MPay24\Init\Action::redirectUrl()
	 * @see \Dfe\Omise\Init\Action::redirectUrl()
	 * @see \Dfe\Paypal\Init\Action::redirectUrl()
	 * @see \Dfe\Paystation\Init\Action::redirectUrl()
	 * @see \Dfe\PostFinance\Init\Action::redirectUrl()
	 * @see \Dfe\Qiwi\Init\Action::redirectUrl()
	 * @see \Dfe\Robokassa\Init\Action::redirectUrl()
	 * @see \Dfe\SecurePay\Init\Action::redirectUrl()
	 * @see \Dfe\Stripe\Init\Action::redirectUrl()
	 * @see \Dfe\Tinkoff\Init\Action::redirectUrl()
	 * @see \Dfe\YandexKassa\Init\Action::redirectUrl()
	 * @return string|null
	 */
	protected function redirectUrl() {return null;}

	/**
	 * 2017-09-10
	 * @used-by action()
	 * @see \Dfe\Qiwi\Init\Action::preorder()
	 * @see \Dfe\Stripe\Init\Action::preorder()
	 */
	protected function preorder() {}

	/**
	 * 2017-03-21
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by preconfigured()
	 * @used-by \Df\GingerPaymentsBase\Init\Action::res()
	 * @used-by \Dfe\AlphaCommerceHub\Init\Action::redirectUrl()
	 * @used-by \Dfe\Omise\Init\Action::redirectUrl()
	 * @used-by \Dfe\Stripe\Init\Action::need3DS()
	 * @used-by \Dfe\Stripe\Init\Action::preorder()
	 * @return S
	 */
	protected function s() {return $this->_m->s();}

	/**
	 * 2017-03-21 Возвращает идентификатор транзакции во внутреннем формате.
	 * 2017-03-26
	 * Stripe-подобные платёжные модули типа Omise
	 * (и в перспективе, после надлежащего рефакторинга — Checkout.com),
	 * осуществляют проверку 3D Secure с перенаправлением браузера.
	 * Они записывают первичную транзакцию в методе @see \Df\StripeClone\Method::chargeNew()
	 * https://github.com/mage2pro/core/blob/2.4.0/StripeClone/Method.php#L117-L164
	 * В этом случае мы намеренно не перекрываем метод transId(), и он возвращает null.
	 * Таким образом @used-by action() не будет записывать первичную транзакцию,
	 * и запись первичной транзакции будет происходить только в @see \Df\StripeClone\Method::chargeNew()
	 * @used-by action()
	 * @see \Df\GingerPaymentsBase\Init\Action::transId()
	 * @see \Df\PaypalClone\Init\Action::transId()
	 * @see \Dfe\Qiwi\Init\Action::transId()
	 * @see \Dfe\Stripe\Init\Action::transId()
	 * @return string|null
	 */
	protected function transId() {return null;}

	/**
	 * 2017-03-21
	 * @param M $m
	 */
	private function __construct(M $m) {$this->_m = $m;}

	/**
	 * 2017-03-21
	 * @used-by action()
	 * @used-by preconfiguredToCapture()
	 * @return string
	 */
	private function preconfigured() {return dfc($this, function() {
		$s = $this->s(); /** @var S $s */
		/** @var string $key */
		$key = 'actionFor' . (df_customer_is_new($this->o()->getCustomerId()) ? 'New' : 'Returned');
		/** @var string $result */
		return $s->v($key, null, function() use($s) {return $s->v('payment_action');}) ?: AC::C;
	});}

	/**
	 * 2017-03-21
	 * @used-by redirectUrl()
	 * @var M
	 */
	private $_m;

	/**
	 * 2017-11-01
	 * @used-by \Df\Payment\Method::getConfigPaymentAction()
	 * @used-by \Dfe\AlphaCommerceHub\Charge::pCharge()
	 * @used-by \Dfe\Stripe\W\Event\Source::ttCurrent()
	 * @param M $m
	 * @return self
	 */
	final static function sg(M $m) {return dfcf(function(M $m) {
		$c = df_con_hier($m, self::class); /** @var string $c */
		return new $c($m);
	}, [$m]);}
}