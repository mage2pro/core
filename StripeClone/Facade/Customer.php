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
	abstract function cardAdd($c, $token);
	
	/**
	 * 2017-02-10
	 * @used-by \Df\StripeClone\Charge::newCard()
	 * @param array(string => mixed) $p
	 * @return object
	 */
	abstract function create(array $p);
	
	/**
	 * 2017-02-10
	 * 2017-02-11
	 * Отныне метод должен вернуть null для удалённого покупателя.
	 * @used-by \Df\StripeClone\ConfigProvider::cards()
	 * @used-by \Df\StripeClone\Charge::newCard()
	 * @param int $id
	 * @return object|null
	 */
	abstract function get($id);

	/**
	 * 2017-02-10
	 * @used-by \Df\StripeClone\Charge::newCard()
	 * @param object $c
	 * @return string
	 */
	abstract function id($c);

	/**
	 * 2017-02-11
	 * @used-by cards()
	 * @param object $c
	 * @return object[]|array(array(string => string))
	 */
	abstract protected function cardsData($c);

	/**
	 * 2017-02-10
	 * @used-by \Df\StripeClone\Charge::newCard()
	 * @param object $c
	 * @return string
	 */
	final function cardIdForJustCreated($c) {
		/** @var ICard $card */
		$card = df_first($this->cards($c));
		return df_result_sne($card->id());
	}

	/**
	 * 2017-02-10
	 * @used-by cardIdForJustCreated()
	 * @used-by \Df\StripeClone\ConfigProvider::cards()
	 * @param object $c
	 * @return ICard[]
	 */
	final function cards($c) {return array_map(function($data) {return
		Card::create($this, $data)
	;}, $this->cardsData($c));}
}