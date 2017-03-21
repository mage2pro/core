<?php
namespace Df\Payment\Init;
use Df\Payment\Method as M;
use Df\Payment\PlaceOrder as PO;
use Df\Payment\Settings as S;
use Df\Payment\Source\AC;
use Magento\Sales\Model\Order as O;
/**
 * 2017-03-21
 * Этот класс не абстрактен и используется некоторыми модулями напрямую.
 * @see \Dfe\Omise\Init\Action
 */
class Action {
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
	protected function action() {return $this->_m->action(function() {
		/** @var string|null $result */
		if (!($result = $this->redirectNeeded() ? null : $this->preconfigured())) {
			/** @var string $url */
			$this->_m->iiaSet(PO::DATA, $url = $this->redirectUrl());
			df_sentry_extra($this->_m, 'Redirect URL', $url);
			// 2016-05-06
			// Postpone sending an order confirmation email to the customer,
			// because the customer should pass 3D Secure validation first.
			// «How is a confirmation email sent on an order placement?» https://mage2.pro/t/1542
			$this->o()->setCanSendNewEmailFlag(false);
		}
		return $result;
	});}

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
	 * @used-by \Df\Payment\Init\Action::redirectUrl()
	 * @return M
	 */
	protected function m() {return $this->_m;}

	/**
	 * 2016-12-24
	 * 2017-01-12
	 * Помимо этого метода имеется также метод @see \Df\StripeClone\Method::redirectNeededForCharge(),
	 * который принимает решение о необходимости проверки 3D Secure
	 * на основании конкретного параметра $charge.
	 * @used-by action()
	 * @see \Dfe\Omise\Init\Action::redirectNeeded()
	 * @return bool
	 */
	protected function redirectNeeded() {return false;}

	/**
	 * 2016-12-24
	 * @used-by getConfigPaymentAction()
	 * @see \Dfe\Omise\Init\Action::redirectUrl()
	 * @return string
	 */
	protected function redirectUrl() {df_abstract($this); return '';}

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
	 * @used-by \Dfe\Omise\Init\Action::redirectNeeded()
	 * @return S
	 */
	protected function s() {return $this->_m->s();}

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