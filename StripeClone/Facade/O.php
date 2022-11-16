<?php
namespace Df\StripeClone\Facade;
use Df\StripeClone\Method as M;
/**
 * 2017-02-11
 * @see \Dfe\Moip\Facade\O
 * @see \Dfe\Omise\Facade\O
 * @see \Dfe\Paymill\Facade\O
 * @see \Dfe\Spryng\Facade\O
 * @see \Dfe\Square\Facade\O
 * @see \Dfe\Stripe\Facade\O
 * @see \Dfe\TBCBank\Facade\O
 * @see \Dfe\Vantiv\Facade\O
 * @method static O s(M $m)
 */
abstract class O extends \Df\Payment\Facade {
	/**
	 * 2016-12-27
	 * 2022-11-17 `object` as an argument type is not supported by PHP < 7.2: https://github.com/mage2pro/core/issues/174
	 * @used-by \Df\StripeClone\Method::transInfo()
	 * @see \Dfe\Moip\Facade\O::toArray()
	 * @see \Dfe\Omise\Facade\O::toArray()
	 * @see \Dfe\Paymill\Facade\O::toArray()
	 * @see \Dfe\Spryng\Facade\O::toArray()
	 * @see \Dfe\Square\Facade\O::toArray()
	 * @see \Dfe\Stripe\Facade\O::toArray()
	 * @see \Dfe\TBCBank\Facade\O::toArray()
	 * @see \Dfe\Vantiv\Facade\O::toArray()
	 * @param object $o
	 * @return array(string => mixed)
	 */
	abstract function toArray($o):array;
}