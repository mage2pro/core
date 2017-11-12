<?php
namespace Df\StripeClone\Facade;
use Df\StripeClone\Method as M;
/**
 * 2017-02-10
 * @see \Dfe\Moip\Facade\Charge
 * @see \Dfe\Omise\Facade\Charge
 * @see \Dfe\Paymill\Facade\Charge
 * @see \Dfe\Spryng\Facade\Charge
 * @see \Dfe\Square\Facade\Charge
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
	 * @see \Dfe\Square\Facade\Charge::capturePreauthorized()
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
	 * @see \Dfe\Square\Facade\Charge::create()
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
	 * @see \Dfe\Square\Facade\Charge::id()
	 * @see \Dfe\Stripe\Facade\Charge::id()
	 * @param object|array(string => mixed) $c
	 * @return string
	 */
	abstract function id($c);

	/**
	 * 2017-02-12
	 * Returns the path to the bank card information
	 * in a charge converted to an array by @see \Df\StripeClone\Facade\O::toArray()
	 * @used-by cardData()
	 * @used-by \Df\StripeClone\Block\Info::cardDataFromChargeResponse()
	 * @see \Dfe\Moip\Facade\Charge::pathToCard()
	 * @see \Dfe\Omise\Facade\Charge::pathToCard()
	 * @see \Dfe\Paymill\Facade\Charge::pathToCard()
	 * @see \Dfe\Spryng\Facade\Charge::pathToCard()
	 * @see \Dfe\Square\Facade\Charge::pathToCard()
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
	 * @see \Dfe\Square\Facade\Charge::refund()
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
	 * @see \Dfe\Square\Facade\Charge::void()
	 * @see \Dfe\Stripe\Facade\Charge::void()
	 * @param string $id
	 * @return object
	 */
	abstract function void($id);

	/**
	 * 2017-02-11 Возвращает использованную при платеже банковскую карту.
	 * @used-by \Df\StripeClone\Method::chargeNew()
	 * @param object|array(string => mixed) $c
	 * @return ICard
	 */
	final function card($c) {return Card::create($this, $this->cardData($c));}

	/**
	 * 2017-06-12
	 * Some PSPs like Moip require 2 steps to make a payment:
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
	final function preorderSet($o) {$this->_preorder = $o;}

	/**
	 * 2017-10-10
	 * The method returns:
	 * 		`true` if $id is an ID of a previously saved bank card.
	 * 		`false` if $id is a new card token.
	 * @used-by \Df\StripeClone\Payer::tokenIsNew()
	 * @used-by \Dfe\Stripe\Method::cardType()
	 * @see \Dfe\Square\Facade\Charge::tokenIsNew()
	 * @see \Dfe\Stripe\Facade\Charge::tokenIsNew()
	 * @param string $id
	 * @return bool
	 */
	function tokenIsNew($id) {return !df_starts_with($id, $this->cardIdPrefix());}

	/**
	 * 2017-02-11
	 * 2017-02-18
	 * Если ПС (как, например, Spryng) не поддерживает сохранение банковской карты
	 * для будущего повторного использования, то этот метод должен вернуть `null`.
	 * Этого достаточно, чтобы @used-by \Df\StripeClone\Payer::tokenIsNew() всегда возвращала `true`.
	 * @used-by tokenIsNew()
	 * @see \Dfe\Moip\Facade\Charge::cardIdPrefix()
	 * @see \Dfe\Omise\Facade\Charge::cardIdPrefix()
	 * @see \Dfe\Paymill\Facade\Charge::cardIdPrefix()
	 * @see \Dfe\Spryng\Facade\Charge::cardIdPrefix()
	 * @return string
	 */
	protected function cardIdPrefix() {return null;}

	/**
	 * 2017-06-13
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Dfe\Moip\Facade\Charge::create()
	 * @return object
	 */
	protected function preorderGet() {return $this->_preorder;}

	/**
	 * 2017-02-11
	 * @used-by card()
	 * @param object|array(string => mixed) $c
	 * @return object|array(string => string)
	 */
	private function cardData($c) {$p = $this->pathToCard(); return
		/** @var object|array(string => string) $r */
		/** @var string $p */
		($r = (is_array($c) || $c instanceof \ArrayAccess) ? $c[$p] : (
			!is_object($c) ? null : (
				/**
				 * 2017-10-08
				 * It is for Paymill:
				 * @uses \Paymill\Models\Response\Transaction::getPayment()
				 * @var callable $callable
				 * https://github.com/mage2pro/paymill-sdk/blob/v4.4.4/lib/Paymill/Models/Response/Transaction.php#L411-L419
				 *		public function getPayment() {
				 *			return $this->_payment;
				 *		}
				 */
				is_callable($callable = [$c, 'get' . ucfirst($p)]) ? call_user_func($callable) :
					/**
					 * 2017-10-08
					 * It is for Spryng:
					 * @uses \SpryngPaymentsApiPhp\Object\Transaction::$card
					 * https://github.com/mage2pro/spryng-sdk/blob/1.2.5/src/Complexity/SpryngPaymentsApiPhp/Object/Transaction.php#L68-L73
					 * 		public $card;
					 */
					dfo($c, $p)
			)
		)) ?: df_error('You should implement cardData().')
	;}

	/**
	 * 2017-06-12
	 * @used-by preorderGet()
	 * @used-by preorderSet()
	 * @var object
	 */
	private $_preorder;
}