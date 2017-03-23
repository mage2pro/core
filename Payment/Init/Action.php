<?php
namespace Df\Payment\Init;
use Df\Payment\Method as M;
use Df\Payment\PlaceOrderInternal as PO;
use Df\Payment\Settings as S;
use Df\Payment\Source\AC;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment\Transaction as T;
/**
 * 2017-03-21
 * Этот класс не абстрактен и используется многими модулями (например: Stripe, Paymill) напрямую.
 * @see \Df\GingerPaymentsBase\Init\Action
 * @see \Df\PaypalClone\Init\Action
 * @see \Dfe\Omise\Init\Action
 */
class Action {
	/**
	 * 2017-03-21
	 * 2016-12-24
	 * По аналогии с @see \Magento\Sales\Model\Order\Payment::processAction()
	 * https://github.com/magento/magento2/blob/2.1.5/app/code/Magento/Sales/Model/Order/Payment.php#L420-L424
	 * @used-by \Df\Payment\Init\Action::redirectUrl()
	 * @return float
	 */
	final protected function amount() {return dfc($this, function() {return $this->_m->cFromBase(
		$this->o()->getBaseTotalDue()
	);});}

	/**
	 * 2017-03-21
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Df\GingerPaymentsBase\Init\Action::res()
	 * @used-by \Df\PaypalClone\Init\Action::charge()
	 * @used-by \Df\PaypalClone\Init\Action::transId()
	 * @return M
	 */
	protected function m() {return $this->_m;}

	/**
	 * 2017-03-21
	 * @used-by action()
	 * @see \Df\PaypalClone\Init\Action::redirectParams()
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
	 * @see \Dfe\Omise\Init\Action::redirectUrl()
	 * @see \Dfe\SecurePay\Init\Action::redirectUrl()
	 * @return string|null
	 */
	protected function redirectUrl() {return null;}

	/**
	 * 2017-03-21
	 * 2016-12-24 Сценарий «Review» неосуществим при необходимости проверки 3D Secure,
	 * ведь администратор не в состоянии пройти проверку 3D Secure за покупателя.
	 * 2017-03-21 Поэтому мы обрабатываем случай «Review» точно так же, как и «Authorize».
	 * @see \Dfe\Omise\Init\Action::redirectUrl()
	 * @return bool
	 */
	final protected function preconfiguredToCapture() {return AC::c($this->preconfigured());}

	/**
	 * 2017-03-21
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by preconfigured()
	 * @used-by \Df\GingerPaymentsBase\Init\Action::res()
	 * @used-by \Dfe\Omise\Init\Action::redirectUrl()
	 * @return S
	 */
	protected function s() {return $this->_m->s();}

	/**
	 * 2017-03-21
	 * Возвращает идентификатор транзакции во внутреннем формате.
	 * @used-by action()
	 * @see \Df\GingerPaymentsBase\Init\Action::transId()
	 * @see \Df\PaypalClone\Init\Action::transId()
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
	 * @used-by p()
	 * @return string|null
	 */
	private function action() {return $this->_m->action(function() {
		/** @var M $m */$m = $this->_m;
		/** @var string|null $url */
		/** @var string|null $result */
		if (!($result = ($url = dfp_url($m, $this->redirectUrl())) ? null : $this->preconfigured())) {
			/** @var array(string => mixed) $p */
			PO::setData($m, $url, $p = $this->redirectParams());
			// 2016-12-20
			if ($this->s()->log()) {
				dfp_report($m, ['Redirect Params' => $p, 'Redirect URL' => $url], 'request');
			}
			// 2016-05-06
			// Postpone sending an order confirmation email to the customer,
			// because the customer should pass 3D Secure validation first.
			// «How is a confirmation email sent on an order placement?» https://mage2.pro/t/1542
			$this->o()->setCanSendNewEmailFlag(false);
			/** @var string|null $id */
			if ($id = $this->transId()) {
				// 2016-07-10
				// Сохраняем информацию о транзакции.
				$m->ii()->setTransactionId($id);
				$m->iiaSetTRR($p);
				/**
				 * 2016-07-10
				 * @uses \Magento\Sales\Model\Order\Payment\Transaction::TYPE_PAYMENT —
				 * это единственный транзакция без специального назначения,
				 * и поэтому мы можем безопасно его использовать
				 * для сохранения информации о нашем запросе к платёжной системе.
				 */
				$m->ii()->addTransaction(T::TYPE_PAYMENT);
			}
		}
		return $result;
	});}

	/**
	 * 2017-03-21
	 * @used-by action()
	 * @used-by amount()
	 * @used-by preconfigured()
	 * @return O
	 */
	private function o() {return $this->_m->o();}

	/**
	 * 2017-03-21
	 * @used-by action()
	 * @used-by preconfiguredToCapture()
	 * @return string
	 */
	private function preconfigured() {return dfc($this, function() {
		/** @var S $s */ $s = $this->s();
		/** @var string $key */
		$key = 'actionFor' . (df_customer_is_new($this->o()->getCustomerId()) ? 'New' : 'Returned');
		/** @var string $result */
		return $s->v($key, null, function() use($s) {return $s->v('payment_action');}) ?: AC::C;
	});}

	/**
	 * 2017-03-21
	 * @var M
	 */
	private $_m;

	/**
	 * 2017-03-21
	 * @param M $m
	 * @return self
	 */
	final static function p(M $m) {
		/** @var string $c */
		$c = df_con_hier($m, self::class);
		/** @var self $i */
		$i = new $c($m);
		return $i->action();
	}
}