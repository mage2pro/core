<?php
namespace Df\StripeClone\Facade;
/**
 * 2017-02-10
 * @see \Dfe\Omise\Facade\Charge
 * @see \Dfe\Paymill\Facade\Charge
 * @see \Dfe\Stripe\Facade\Charge
 */
abstract class Charge extends \Df\StripeClone\Facade {
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
	 * 2017-02-11
	 * Информация о банковской карте.
	 * «How is the \Magento\Sales\Model\Order\Payment's setCcLast4() / getCcLast4() used?»
	 * https://mage2.pro/t/941
	 * @used-by \Df\StripeClone\Method::chargeNew()
	 * @see \Dfe\Omise\Facade\Charge::card()
	 * @see \Dfe\Paymill\Facade\Charge::card()
	 * @see \Dfe\Stripe\Facade\Charge::card()
	 * @param object $c
	 * @return array(string => string)
	 */
	abstract public function card($c);

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
}