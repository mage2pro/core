<?php
namespace Df\StripeClone\Facade;
/**
 * 2017-02-11
 * @see \Dfe\Omise\Facade\Card
 * @see \Dfe\Paymill\Facade\Card
 * @see \Dfe\Spryng\Facade\Card
 * @see \Dfe\Stripe\Facade\Card
 */
interface ICard {
	/**
	 * 2017-02-11
	 * @used-by \Df\StripeClone\CardFormatter::ii()
	 * @used-by \Df\StripeClone\CardFormatter::label()
	 * @return string
	 */
	function brand();

	/**
	 * 2017-02-11
	 * @used-by \Df\StripeClone\CardFormatter::country()
	 * @return string
	 */
	function country();

	/**
	 * 2017-02-11
	 * @used-by \Df\StripeClone\CardFormatter::exp()
	 * @used-by \Df\StripeClone\CardFormatter::ii()
	 * @return string
	 */
	function expMonth();

	/**
	 * 2017-02-11
	 * @used-by \Df\StripeClone\CardFormatter::exp()
	 * @used-by \Df\StripeClone\CardFormatter::ii()
	 * @return string
	 */
	function expYear();

	/**
	 * 2017-02-11
	 * @used-by \Df\StripeClone\ConfigProvider::cards()
	 * @used-by \Df\StripeClone\Facade\Customer::cardIdForJustCreated()
	 * @return string
	 */
	function id();

	/**
	 * 2017-02-11
	 * @used-by \Df\StripeClone\CardFormatter::ii()
	 * @return string
	 */
	function owner();

	/**
	 * 2017-02-11
	 * @used-by \Df\StripeClone\CardFormatter::ii()
	 * @used-by \Df\StripeClone\CardFormatter::label()
	 * @return string
	 */
	function last4();
}