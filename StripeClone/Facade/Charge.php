<?php
namespace Df\StripeClone\Facade;
use Df\StripeClone\Method as M;
use Magento\Payment\Model\Info as I;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Quote\Model\Quote\Payment as QP;
use Magento\Sales\Model\Order\Creditmemo as CM;
use Magento\Sales\Model\Order\Payment as OP;
/**
 * 2017-02-10
 * @see \Dfe\Omise\Facade\Charge
 * @see \Dfe\Paymill\Facade\Charge
 * @see \Dfe\Stripe\Facade\Charge
 */
abstract class Charge {
	/**
	 * 2017-02-10
	 * @used-by \Df\StripeClone\Method::charge()
	 * @see \Dfe\Omise\Facade\Charge::capturePreauthorized()
	 * @see \Dfe\Paymill\Facade\Charge::capturePreauthorized()
	 * @see \Dfe\Stripe\Facade\Charge::capturePreauthorized()
	 * @param string $id
	 * @return object
	 */
	abstract public function capturePreauthorized($id);

	/**
	 * 2017-02-10
	 * @used-by \Df\StripeClone\Method::chargeNew()
	 * @see \Dfe\Omise\Facade\Charge::create()
	 * @see \Dfe\Paymill\Facade\Charge::create()
	 * @see \Dfe\Stripe\Facade\Charge::create()
	 * @param array(string => mixed) $p
	 * @return object
	 */
	abstract public function create(array $p);

	/**
	 * 2017-02-10
	 * @used-by \Df\StripeClone\Method::chargeNew()
	 * @see \Dfe\Omise\Facade\Charge::id()
	 * @see \Dfe\Paymill\Facade\Charge::id()
	 * @see \Dfe\Stripe\Facade\Charge::id()
	 * @param object $c
	 * @return string
	 */
	abstract public function id($c);

	/**
	 * 2017-02-10
	 * Метод должен вернуть библиотечный объект API платёжной системы.
	 * @used-by \Df\StripeClone\Method::_refund()
	 * @see \Dfe\Omise\Facade\Charge::refund()
	 * @see \Dfe\Paymill\Facade\Charge::refund()
	 * @see \Dfe\Stripe\Facade\Charge::refund()
	 * @param string $id
	 * @param float $amount
	 * В формате и валюте платёжной системы.
	 * Значение готово для применения в запросе API.
	 * @return object
	 */
	abstract public function refund($id, $amount);

	/**
	 * 2017-02-10
	 * Метод должен вернуть библиотечный объект API платёжной системы.
	 * @used-by \Df\StripeClone\Method::_refund()
	 * @see \Dfe\Omise\Facade\Charge::void()
	 * @see \Dfe\Paymill\Facade\Charge::void()
	 * @see \Dfe\Stripe\Facade\Charge::void()
	 * @param string $id
	 * @return object
	 */
	abstract public function void($id);

	/**
	 * 2017-02-10
	 * @used-by \Df\StripeClone\Method::fCharge()
	 * @param M $m
	 */
	final public function __construct(M $m) {$this->_m = $m;}

	/**
	 * 2016-12-10
	 * @used-by \Dfe\Stripe\Facade\Charge::refundMeta()
	 * @used-by \Dfe\Stripe\Facade\Charge::refundAdjustments()
	 * @return CM|null
	 */
	final protected function cm() {return $this->ii()->getCreditmemo();}

	/**
	 * 2016-12-10
	 * @used-by cm()
	 * @return II|I|OP|QP
	 */
	private function ii() {return $this->m()->getInfoInstance();}

	/**
	 * 2016-12-10
	 * @used-by ii()
	 * @return M
	 */
	private function m() {return $this->_m;}

	/**
	 * 2017-02-10
	 * @used-by __construct()
	 * @used-by m()
	 * @var M
	 */
	private $_m;
}