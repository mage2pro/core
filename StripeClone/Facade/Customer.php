<?php
namespace Df\StripeClone\Facade;
/**
 * 2017-02-10
 * @see \Dfe\Omise\Facade\Customer
 * @see \Dfe\Paymill\Facade\Customer
 * @see \Dfe\Stripe\Facade\Customer
 */
abstract class Customer extends \Df\StripeClone\Facade {
	/**
	 * 2017-02-10
	 * @used-by \Df\StripeClone\Charge::newCard()
	 * @param object $c  
	 * @param string $token
	 * @return string
	 */
	abstract public function cardAdd($c, $token);

	/**
	 * 2017-02-10
	 * @used-by cardIdForJustCreated()
	 * @used-by \Df\StripeClone\ConfigProvider::cards()
	 * @param object $c
	 * @return ICard[]
	 */
	abstract public function cards($c);
	
	/**
	 * 2017-02-10
	 * @used-by \Df\StripeClone\Charge::newCard()
	 * @param array(string => mixed) $p
	 * @return object
	 */
	abstract public function create(array $p);	
	
	/**
	 * 2017-02-10
	 * 2017-02-11
	 * Отныне метод должен вернуть null для удалённого покупателя.
	 * @used-by \Df\StripeClone\ConfigProvider::cards()
	 * @used-by \Df\StripeClone\Charge::newCard()
	 * @param int $id
	 * @return object|null
	 */
	abstract public function get($id);

	/**
	 * 2017-02-10
	 * @used-by \Df\StripeClone\Charge::newCard()
	 * @param object $c
	 * @return string
	 */
	abstract public function id($c);

	/**
	 * 2017-02-10
	 * @used-by \Df\StripeClone\Charge::newCard()
	 * @param object $c
	 * @return string
	 */
	final public function cardIdForJustCreated($c) {
		/** @var ICard $card */
		$card = df_first($this->cards($c));
		return df_result_sne($card->id());
	}
}