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
 * @see \Dfe\TBCBank\Facade\Charge
 * @see \Dfe\Vantiv\Facade\Charge
 * @method static Charge s(M $m)
 */
abstract class Charge extends \Df\Payment\Facade {
	/**
	 * 2017-02-10
	 * 2022-11-12 `object` as a return type is not supported by PHP < 7.2: https://3v4l.org/dAmcs
	 * 2024-06-03 We need to support PHP ≥ 7.1.
	 * @used-by \Df\StripeClone\Method::charge()
	 * @see \Dfe\Moip\Facade\Charge::capturePreauthorized()
	 * @see \Dfe\Omise\Facade\Charge::capturePreauthorized()
	 * @see \Dfe\Paymill\Facade\Charge::capturePreauthorized()
	 * @see \Dfe\Spryng\Facade\Charge::capturePreauthorized()
	 * @see \Dfe\Square\Facade\Charge::capturePreauthorized()
	 * @see \Dfe\Stripe\Facade\Charge::capturePreauthorized()
	 * @see \Dfe\TBCBank\Facade\Charge::capturePreauthorized()
	 * @see \Dfe\Vantiv\Facade\Charge::capturePreauthorized()
	 * @param int|float $a
	 * The $a value is already converted to the PSP currency and formatted according to the PSP requirements.
	 * @return object
	 */
	abstract function capturePreauthorized(string $id, $a);

	/**
	 * 2017-02-10
	 * 2022-11-12 `object` as a return type is not supported by PHP < 7.2: https://3v4l.org/dAmcs
	 * @used-by \Df\StripeClone\Method::chargeNew()
	 * @see \Dfe\Moip\Facade\Charge::create()
	 * @see \Dfe\Omise\Facade\Charge::create()
	 * @see \Dfe\Paymill\Facade\Charge::create()
	 * @see \Dfe\Spryng\Facade\Charge::create()
	 * @see \Dfe\Square\Facade\Charge::create()
	 * @see \Dfe\Stripe\Facade\Charge::create()
	 * @see \Dfe\Vantiv\Facade\Charge::create()
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
	 * @see \Dfe\TBCBank\Facade\Charge::id()
	 * @see \Dfe\Vantiv\Facade\Charge::id()
	 * @param object|array(string => mixed) $c
	 */
	abstract function id($c):string;

	/**
	 * 2017-02-12
	 * Returns the path to the bank card information
	 * in a charge converted to an array by @see \Df\StripeClone\Facade\O::toArray()
	 * @used-by self::cardData()
	 * @used-by \Df\StripeClone\Block\Info::cardDataFromChargeResponse()
	 * @see \Dfe\Moip\Facade\Charge::pathToCard()
	 * @see \Dfe\Omise\Facade\Charge::pathToCard()
	 * @see \Dfe\Paymill\Facade\Charge::pathToCard()
	 * @see \Dfe\Spryng\Facade\Charge::pathToCard()
	 * @see \Dfe\Square\Facade\Charge::pathToCard()
	 * @see \Dfe\Stripe\Facade\Charge::pathToCard()
	 * @see \Dfe\TBCBank\Facade\Charge::pathToCard()
	 * @see \Dfe\Vantiv\Facade\Charge::pathToCard()
	 * @return string|null
	 */
	abstract function pathToCard();

	/**
	 * 2017-02-10 Метод должен вернуть библиотечный объект API платёжной системы.
	 * 2022-11-12 `object` as a return type is not supported by PHP < 7.2: https://3v4l.org/dAmcs
	 * 2022-12-19 The $a value is already converted to the PSP currency and formatted according to the PSP requirements.
	 * @used-by \Df\StripeClone\Method::_refund()
	 * @see \Dfe\Moip\Facade\Charge::refund()
	 * @see \Dfe\Omise\Facade\Charge::refund()
	 * @see \Dfe\Paymill\Facade\Charge::refund()
	 * @see \Dfe\Spryng\Facade\Charge::refund()
	 * @see \Dfe\Square\Facade\Charge::refund()
	 * @see \Dfe\Stripe\Facade\Charge::refund()
	 * @see \Dfe\TBCBank\Facade\Charge::refund()
	 * @see \Dfe\Vantiv\Facade\Charge::refund()
	 * @return object
	 */
	abstract function refund(string $id, int $a);

	/**
	 * 2017-02-10 Метод должен вернуть библиотечный объект API платёжной системы.
	 * 2022-11-12 `object` as a return type is not supported by PHP < 7.2: https://3v4l.org/dAmcs
	 * @used-by \Df\StripeClone\Method::_refund()
	 * @see \Dfe\Moip\Facade\Charge::void()
	 * @see \Dfe\Omise\Facade\Charge::void()
	 * @see \Dfe\Paymill\Facade\Charge::void()
	 * @see \Dfe\Spryng\Facade\Charge::void()
	 * @see \Dfe\Square\Facade\Charge::void()
	 * @see \Dfe\Stripe\Facade\Charge::void()
	 * @see \Dfe\TBCBank\Facade\Charge::void()
	 * @see \Dfe\Vantiv\Facade\Charge::void()
	 * @return object
	 */
	abstract function void(string $id);

	/**
	 * 2017-02-11 Возвращает использованную при платеже банковскую карту.
	 * @used-by \Df\StripeClone\Method::chargeNew()
	 * @see \Dfe\Vantiv\Facade\Charge::card()
	 * @param object|array(string => mixed) $c
	 */
	function card($c):Card {return Card::create($this, $this->cardData($c));}

	/**
	 * 2017-06-12
	 * Some PSPs like Moip require 2 steps to make a payment:
	 * 1) Creating an «order».
	 * 2) Creating a «payment».
	 * @used-by \Df\StripeClone\Method::chargeNew()
	 * @see \Dfe\Moip\Facade\Charge::needPreorder()
	 */
	function needPreorder():bool {return false;}

	/**
	 * 2017-06-12
	 * 2022-11-17
	 * `object` as an argument type is not supported by PHP < 7.2:
	 * https://github.com/mage2pro/core/issues/174#user-content-object
	 * @used-by \Df\StripeClone\Method::chargeNew()
	 * @param object $o
	 */
	final function preorderSet($o):void {$this->_preorder = $o;}

	/**
	 * 2017-10-10
	 * The method returns:
	 * 		`true` if $id is an ID of a previously saved bank card.
	 * 		`false` if $id is a new card token.
	 * @used-by \Df\StripeClone\Payer::tokenIsNew()
	 * @used-by \Dfe\Stripe\Method::cardType()
	 * @see \Dfe\Square\Facade\Charge::tokenIsNew()
	 * @see \Dfe\Stripe\Facade\Charge::tokenIsNew()
	 * @see \Dfe\TBCBank\Facade\Charge::tokenIsNew()
	 */
	function tokenIsNew(string $id):bool {return !df_starts_with($id, $this->cardIdPrefix());}

	/**
	 * 2017-02-11
	 * 2017-02-18
	 * Если ПС (как, например, Spryng) не поддерживает сохранение банковской карты
	 * для будущего повторного использования, то этот метод должен вернуть `null`.
	 * Этого достаточно, чтобы @used-by \Df\StripeClone\Payer::tokenIsNew() всегда возвращала `true`.
	 * @used-by self::tokenIsNew()
	 * @see \Dfe\Moip\Facade\Charge::cardIdPrefix()
	 * @see \Dfe\Omise\Facade\Charge::cardIdPrefix()
	 * @see \Dfe\Paymill\Facade\Charge::cardIdPrefix()
	 * @see \Dfe\Spryng\Facade\Charge::cardIdPrefix()
	 */
	protected function cardIdPrefix():string {return '';}

	/**
	 * 2017-06-13
	 * 2022-11-12 `object` as a return type is not supported by PHP < 7.2: https://3v4l.org/dAmcs
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Dfe\Moip\Facade\Charge::create()
	 * @return object
	 */
	protected function preorderGet() {return $this->_preorder;}

	/**
	 * 2017-02-11
	 * @used-by self::card()
	 * @param object|array(string => mixed) $c
	 * @return object|array(string => string)
	 */
	private function cardData($c) {$p = $this->pathToCard(); /** @var string $p */ return
		((is_array($c) || $c instanceof \ArrayAccess) ? $c[$p] : (
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
	 * @used-by self::preorderGet()
	 * @used-by self::preorderSet()
	 * @var object
	 */
	private $_preorder;
}