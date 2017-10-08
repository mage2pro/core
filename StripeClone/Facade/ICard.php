<?php
namespace Df\StripeClone\Facade;
/**
 * 2017-02-11   
 * @see \Dfe\Moip\Facade\Card
 * @see \Dfe\Omise\Facade\Card
 * @see \Dfe\Paymill\Facade\Card
 * @see \Dfe\Spryng\Facade\Card
 * @see \Dfe\Square\Facade\Card
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
	 * 2017-10-07 It should be an ISO-2 code or `null`.
	 * https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2#Officially_assigned_code_elements
	 * @used-by \Df\StripeClone\CardFormatter::country()
	 * @return string|null
	 */
	function country();

	/**
	 * 2017-02-11
	 * @used-by \Df\StripeClone\CardFormatter::exp()
	 * @used-by \Df\StripeClone\CardFormatter::ii()
	 * @return int
	 */
	function expMonth();

	/**
	 * 2017-02-11    
	 * 2017-07-19 Some PSPs like Moip does not return the card's expiration date.
	 * https://github.com/mage2pro/moip/blob/0.7.6/Facade/Card.php#L84-L104
	 * @used-by \Df\StripeClone\CardFormatter::exp()
	 * @used-by \Df\StripeClone\CardFormatter::ii()
	 * @return int|null
	 */
	function expYear();

	/**
	 * 2017-02-11
	 * @used-by \Df\StripeClone\ConfigProvider::cards()
	 * @used-by \Df\StripeClone\Facade\Customer::cardIdForJustCreated()
	 * @return string|null
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