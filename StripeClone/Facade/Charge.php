<?php
namespace Df\StripeClone\Facade;
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
	 * @param string|object|null $m [optional]
	 * @return self
	 */
	final public static function s($m = null) {return
		df_o(df_con_heir($m ?: static::class, __CLASS__))
	;}
}