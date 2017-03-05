<?php
namespace Df\StripeClone\Facade;
/**
 * 2017-02-10
 * @see \Dfe\Omise\Facade\Customer
 * @see \Dfe\Paymill\Facade\Customer
 * @see \Dfe\Spryng\Facade\Customer
 * @see \Dfe\Stripe\Facade\Customer
 */
abstract class Customer extends \Df\Payment\Facade {
	/**
	 * 2017-02-10
	 * @used-by \Df\StripeClone\Charge::newCard()
	 * @see \Dfe\Omise\Facade\Customer::cardAdd()
	 * @see \Dfe\Paymill\Facade\Customer::cardAdd()
	 * @see \Dfe\Spryng\Facade\Customer::cardAdd()
	 * @see \Dfe\Stripe\Facade\Customer::cardAdd()
	 * @param object $c  
	 * @param string $token
	 * @return string
	 */
	abstract function cardAdd($c, $token);
	
	/**
	 * 2017-02-10
	 * Этот метод должен регистрировать в ПС не только покупателя, но и его банковскую карту.
	 * Stripe и Omise умеют делать это сразу (в ответ на единый запрос к ПС),
	 * а вот для Paymill банковскую карту надо регистрировать отдельным запросом к ПС.
	 * @used-by \Df\StripeClone\Charge::newCard()
	 * @see \Dfe\Omise\Facade\Customer::create()
	 * @see \Dfe\Paymill\Facade\Customer::create()
	 * @see \Dfe\Spryng\Facade\Customer::create()
	 * @see \Dfe\Stripe\Facade\Customer::create()
	 * @param array(string => mixed) $p
	 * @return object
	 */
	abstract function create(array $p);
	
	/**
	 * 2017-02-10
	 * 2017-02-11
	 * Отныне метод должен вернуть null для удалённого покупателя.
	 * @used-by get()
	 * @see \Dfe\Omise\Facade\Customer::_get()
	 * @see \Dfe\Paymill\Facade\Customer::_get()
	 * @see \Dfe\Spryng\Facade\Customer::_get()
	 * @see \Dfe\Stripe\Facade\Customer::_get()
	 * @param int $id
	 * @return object|null
	 */
	abstract function _get($id);

	/**
	 * 2017-02-10
	 * @used-by \Df\StripeClone\Charge::newCard()
	 * @see \Dfe\Omise\Facade\Customer::id()
	 * @see \Dfe\Paymill\Facade\Customer::id()
	 * @see \Dfe\Spryng\Facade\Customer::id()
	 * @see \Dfe\Stripe\Facade\Customer::id()
	 * @param object $c
	 * @return string
	 */
	abstract function id($c);

	/**
	 * 2017-02-11
	 * @used-by cards()
	 * @see \Dfe\Omise\Facade\Customer::cardsData()
	 * @see \Dfe\Paymill\Facade\Customer::cardsData()
	 * @see \Dfe\Spryng\Facade\Customer::cardsData()
	 * @see \Dfe\Stripe\Facade\Customer::cardsData()
	 * @param object $c
	 * @return object[]|array(array(string => string))
	 */
	abstract protected function cardsData($c);

	/**
	 * 2017-02-10
	 * 2017-02-18
	 * Добавил обработку ПС (Spryng), которые не поддерживают сохранение карт.
	 * @used-by \Df\StripeClone\Charge::newCard()
	 * @param object $c
	 * @return string|null
	 */
	final function cardIdForJustCreated($c) {/** @var ICard|null $card */return
		!($card = df_first($this->cards($c))) ? null : df_result_sne($card->id())
	;}

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

	/**
	 * 2017-02-10
	 * 2017-02-11
	 * Отныне метод должен вернуть null для удалённого покупателя.
	 * 2017-02-24
	 * «I have switched my Stripe account and got the «No such customer» error»: https://mage2.pro/t/3337
	 * @used-by \Df\StripeClone\ConfigProvider::cards()
	 * @used-by \Df\StripeClone\Charge::newCard()
	 * @param int $id
	 * @return object|null
	 */
	final function get($id) {try {return $this->_get($id);} catch (\Exception $e) {return null;}}
}