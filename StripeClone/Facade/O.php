<?php
namespace Df\StripeClone\Facade;
use Df\StripeClone\Method as M;
/**
 * 2017-02-11
 * @see \Dfe\Omise\Facade\O
 * @see \Dfe\Paymill\Facade\O
 * @see \Dfe\Stripe\Facade\O
 * @method static O s(M $m)
 */
abstract class O extends \Df\StripeClone\Facade {
	/**
	 * 2016-12-27
	 * @used-by \Df\StripeClone\Method::transInfo()  
	 * @see \Dfe\Omise\Facade\O::toArray()
	 * @see \Dfe\Paymill\Facade\O::toArray()
	 * @see \Dfe\Stripe\Facade\O::toArray()
	 * @param object $o
	 * @return array(string => mixed)
	 */
	abstract public function toArray($o);
}