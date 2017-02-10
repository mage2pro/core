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
	 * @return array(string => string)
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
	 * @used-by \Df\StripeClone\ConfigProvider::cards()
	 * @used-by \Df\StripeClone\Charge::newCard()
	 * @param int $id
	 * @return object
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
	 * @used-by \Df\StripeClone\ConfigProvider::cards()
	 * @used-by \Df\StripeClone\Charge::newCard()
	 * @param object $c
	 * @return bool
	 */
	abstract public function isDeleted($c);

	/**
	 * 2017-02-10
	 * @used-by \Df\StripeClone\Charge::newCard()
	 * @param object $c
	 * @return string
	 */
	final public function cardIdForJustCreated($c) {return df_result_sne(
		df_first($this->cards($c))['id']
	);}
}