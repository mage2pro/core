<?php
namespace Df\StripeClone\Facade;
use Df\StripeClone\Method as M;
/**
 * 2017-02-10
 * @see \Dfe\Moip\Facade\Charge
 * @see \Dfe\Omise\Facade\Charge
 * @see \Dfe\Paymill\Facade\Charge
 * @see \Dfe\Spryng\Facade\Charge
 * @see \Dfe\Stripe\Facade\Charge
 * @method static Charge s(M $m)
 */
abstract class Charge extends \Df\Payment\Facade {
	/**
	 * 2017-02-10
	 * @used-by \Df\StripeClone\Method::charge()
	 * @see \Dfe\Moip\Facade\Charge::capturePreauthorized()
	 * @see \Dfe\Omise\Facade\Charge::capturePreauthorized()
	 * @see \Dfe\Paymill\Facade\Charge::capturePreauthorized()
	 * @see \Dfe\Spryng\Facade\Charge::capturePreauthorized()
	 * @see \Dfe\Stripe\Facade\Charge::capturePreauthorized()
	 * @param string $id
	 * @param int|float $a
	 * The $a value is already converted to the PSP currency and formatted according to the PSP requirements.
	 * @return object
	 */
	abstract function capturePreauthorized($id, $a);

	/**
	 * 2017-02-10
	 * @used-by \Df\StripeClone\Method::chargeNew()
	 * @see \Dfe\Moip\Facade\Charge::create()
	 * @see \Dfe\Omise\Facade\Charge::create()
	 * @see \Dfe\Paymill\Facade\Charge::create()
	 * @see \Dfe\Spryng\Facade\Charge::create()
	 * @see \Dfe\Stripe\Facade\Charge::create()
	 * @param array(string => mixed) $p
	 * @return object
	 */
	abstract function create(array $p);

	/**
	 * 2017-02-10
	 * @used-by \Df\StripeClone\Method::chargeNew()
	 * @see \Dfe\Moip\Facade\Charge::id()
	 * @see \Dfe\Omise\Facade\Charge::id()
	 * @see \Dfe\Paymill\Facade\Charge::id()
	 * @see \Dfe\Spryng\Facade\Charge::id()
	 * @see \Dfe\Stripe\Facade\Charge::id()
	 * @param object $c
	 * @return string
	 */
	abstract function id($c);

	/**
	 * 2017-02-12
	 * Returns the path to the bank card information
	 * in a charge converted to an array by @see \Df\StripeClone\Facade\O::toArray()
	 * @used-by \Df\StripeClone\Block\Info::prepare()
	 * @see \Dfe\Moip\Facade\Charge::pathToCard()
	 * @see \Dfe\Omise\Facade\Charge::pathToCard()
	 * @see \Dfe\Paymill\Facade\Charge::pathToCard()
	 * @see \Dfe\Spryng\Facade\Charge::pathToCard()
	 * @see \Dfe\Stripe\Facade\Charge::pathToCard()
	 * @return string
	 */
	abstract function pathToCard();

	/**
	 * 2017-02-10
	 * Метод должен вернуть библиотечный объект API платёжной системы.
	 * @used-by \Df\StripeClone\Method::_refund()
	 * @see \Dfe\Moip\Facade\Charge::refund()
	 * @see \Dfe\Omise\Facade\Charge::refund()
	 * @see \Dfe\Paymill\Facade\Charge::refund()
	 * @see \Dfe\Spryng\Facade\Charge::refund()
	 * @see \Dfe\Stripe\Facade\Charge::refund()
	 * @param string $id
	 * @param int|float $a
	 * The $a value is already converted to the PSP currency and formatted according to the PSP requirements.
	 * @return object
	 */
	abstract function refund($id, $a);

	/**
	 * 2017-02-10
	 * Метод должен вернуть библиотечный объект API платёжной системы.
	 * @used-by \Df\StripeClone\Method::_refund()
	 * @see \Dfe\Moip\Facade\Charge::void()
	 * @see \Dfe\Omise\Facade\Charge::void()
	 * @see \Dfe\Paymill\Facade\Charge::void()
	 * @see \Dfe\Spryng\Facade\Charge::void()
	 * @see \Dfe\Stripe\Facade\Charge::void()
	 * @param string $id
	 * @return object
	 */
	abstract function void($id);

	/**
	 * 2017-02-11
	 * @used-by card()
	 * @see \Dfe\Moip\Facade\Charge::cardData()
	 * @see \Dfe\Omise\Facade\Charge::cardData()
	 * @see \Dfe\Paymill\Facade\Charge::cardData()
	 * @see \Dfe\Spryng\Facade\Charge::cardData()
	 * @see \Dfe\Stripe\Facade\Charge::cardData()
	 * @param object $c
	 * @return object|array(string => string)
	 */
	abstract protected function cardData($c);

	/**
	 * 2017-02-11
	 * Возвращает использованную при платеже банковскую карту.
	 * @used-by \Df\StripeClone\Method::chargeNew()
	 * @param object $c
	 * @return ICard
	 */
	final function card($c) {return Card::create($this, $this->cardData($c));}

	/**
	 * 2017-06-12
	 * Some PSPs like Moip requires 2 steps to make a payment:
	 * 1) Creating an «order».
	 * 2) Creating a «payment».
	 * @used-by \Df\StripeClone\Method::chargeNew()
	 * @see \Dfe\Moip\Facade\Charge::needPreorder()
	 * @return bool
	 */
	function needPreorder() {return false;}

	/**
	 * 2017-06-12
	 * @used-by \Df\StripeClone\Method::chargeNew()
	 * @param object $o
	 */
	final function setPreorder($o) {$this->_preorder = $o;}

	/**
	 * 2017-06-12
	 * @used-by setPreorder()
	 * @used-by \Dfe\Moip\Facade\Charge::create()
	 * @var object
	 */
	protected $_preorder;
}