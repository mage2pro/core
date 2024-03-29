<?php
namespace Df\StripeClone\Facade;
/**
 * 2017-01-11
 * @see \Dfe\Moip\Facade\Card
 * @see \Dfe\Omise\Facade\Card
 * @see \Dfe\Paymill\Facade\Card
 * @see \Dfe\Spryng\Facade\Card
 * @see \Dfe\Square\Facade\Card
 * @see \Dfe\Stripe\Facade\Card
 * @see \Dfe\TBCBank\Facade\Card
 * @see \Dfe\Vantiv\Facade\Card
 */
abstract class Card {
	/**
	 * 2017-02-11
	 * @used-by \Df\StripeClone\CardFormatter::ii()
	 * @used-by \Df\StripeClone\CardFormatter::label()
	 * @see \Dfe\Moip\Facade\Card::brand()
	 * @see \Dfe\Omise\Facade\Card::brand()
	 * @see \Dfe\Paymill\Facade\Card::brand()
	 * @see \Dfe\Spryng\Facade\Card::brand()
	 * @see \Dfe\Square\Facade\Card::brand()
	 * @see \Dfe\Stripe\Facade\Card::brand()
	 * @see \Dfe\TBCBank\Facade\Card::brand()
	 * @see \Dfe\Vantiv\Facade\Card::brand()
	 */
	abstract function brand():string;

	/**
	 * 2017-02-11
	 * 2017-10-07 It should be an ISO-2 code or `null`.
	 * https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2#Officially_assigned_code_elements
	 * @used-by \Df\StripeClone\CardFormatter::country()
	 * @see \Dfe\Moip\Facade\Card::country()
	 * @see \Dfe\Omise\Facade\Card::country()
	 * @see \Dfe\Paymill\Facade\Card::country()
	 * @see \Dfe\Spryng\Facade\Card::country()
	 * @see \Dfe\Square\Facade\Card::country()
	 * @see \Dfe\Stripe\Facade\Card::country()
	 * @see \Dfe\TBCBank\Facade\Card::country()
	 * @see \Dfe\Vantiv\Facade\Card::country()
	 * @return string|null
	 */
	abstract function country();

	/**
	 * 2017-02-11
	 * @used-by self::isActive()
	 * @used-by \Df\StripeClone\CardFormatter::exp()
	 * @used-by \Df\StripeClone\CardFormatter::ii()
	 * @see \Dfe\Moip\Facade\Card::expMonth()
	 * @see \Dfe\Omise\Facade\Card::expMonth()
	 * @see \Dfe\Paymill\Facade\Card::expMonth()
	 * @see \Dfe\Spryng\Facade\Card::expMonth()
	 * @see \Dfe\Square\Facade\Card::expMonth()
	 * @see \Dfe\Stripe\Facade\Card::expMonth()
	 * @see \Dfe\TBCBank\Facade\Card::expMonth()
	 * @see \Dfe\Vantiv\Facade\Card::expMonth()
	 * @return int|null
	 */
	abstract function expMonth();

	/**
	 * 2017-02-11
	 * 2017-07-19 Some PSPs like Moip does not return the card's expiration date.
	 * https://github.com/mage2pro/moip/blob/0.7.6/Facade/Card.php#L84-L104
	 * 2018-11-13 If value is exist, then it should be a 4-digits integer.
	 * @used-by self::isActive()
	 * @used-by \Df\StripeClone\CardFormatter::exp()
	 * @used-by \Df\StripeClone\CardFormatter::ii()
	 * @see \Dfe\Moip\Facade\Card::expYear()
	 * @see \Dfe\Omise\Facade\Card::expYear()
	 * @see \Dfe\Paymill\Facade\Card::expYear()
	 * @see \Dfe\Spryng\Facade\Card::expYear()
	 * @see \Dfe\Square\Facade\Card::expYear()
	 * @see \Dfe\Stripe\Facade\Card::expYear()
	 * @see \Dfe\TBCBank\Facade\Card::expYear()
	 * @see \Dfe\Vantiv\Facade\Card::expYear()
	 * @return int|null
	 */
	abstract function expYear();

	/**
	 * 2017-02-11
	 * @used-by \Df\StripeClone\ConfigProvider::cards()
	 * @used-by \Df\StripeClone\Facade\Customer::cardIdForJustCreated()
	 * @see \Dfe\Moip\Facade\Card::id()
	 * @see \Dfe\Omise\Facade\Card::id()
	 * @see \Dfe\Paymill\Facade\Card::id()
	 * @see \Dfe\Spryng\Facade\Card::id()
	 * @see \Dfe\Square\Facade\Card::id()
	 * @see \Dfe\Stripe\Facade\Card::id()
	 * @see \Dfe\TBCBank\Facade\Card::id()
	 * @see \Dfe\Vantiv\Facade\Card::id()
	 * @return string|null
	 */
	abstract function id();

	/**
	 * 2017-02-11
	 * @used-by \Df\StripeClone\CardFormatter::ii()
	 * @see \Dfe\Moip\Facade\Card::owner()
	 * @see \Dfe\Omise\Facade\Card::owner()
	 * @see \Dfe\Paymill\Facade\Card::owner()
	 * @see \Dfe\Spryng\Facade\Card::owner()
	 * @see \Dfe\Square\Facade\Card::owner()
	 * @see \Dfe\Stripe\Facade\Card::owner()
	 * @see \Dfe\TBCBank\Facade\Card::owner()
	 * @see \Dfe\Vantiv\Facade\Card::owner()
	 * @return string|null
	 */
	abstract function owner();

	/**
	 * 2017-02-11
	 * @used-by \Df\StripeClone\CardFormatter::ii()
	 * @used-by \Df\StripeClone\CardFormatter::label()
	 * @see \Dfe\Moip\Facade\Card::last4()
	 * @see \Dfe\Omise\Facade\Card::last4()
	 * @see \Dfe\Paymill\Facade\Card::last4()
	 * @see \Dfe\Spryng\Facade\Card::last4()
	 * @see \Dfe\Square\Facade\Card::last4()
	 * @see \Dfe\Stripe\Facade\Card::last4()
	 * @see \Dfe\TBCBank\Facade\Card::last4()
	 * @see \Dfe\Vantiv\Facade\Card::last4()
	 * @return string|null
	 */
	abstract function last4();

	/**
	 * 2018-11-13
	 * @used-by \Df\StripeClone\Facade\Customer::cardsActive()
	 */
	final function isActive():bool {return /** @var int $y1 */ /** @var int $y2 */
		($y1 = df_year()) < ($y2 = $this->expYear()) || ($y1 === $y2 && df_month() <= $this->expMonth())
	;}

	/**
	 * 2017-01-11
	 * @used-by \Df\StripeClone\Block\Info::card()
	 * @used-by \Df\StripeClone\Facade\Charge::card() 
	 * @used-by \Df\StripeClone\Facade\Customer::cards()
	 * @used-by \Dfe\Vantiv\Method::card()
	 * @param string|object $m
	 * @param object|array(string => string) $data
	 */
	static function create($m, $data):self {return df_new(df_con_heir($m, self::class), $data);}
}