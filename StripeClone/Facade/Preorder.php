<?php
namespace Df\StripeClone\Facade;
use Df\StripeClone\Method as M;
/**
 * 2017-06-12
 * Some PSPs like Moip require 2 steps to make a payment:
 * 1) Creating an «order».
 * 2) Creating a «payment».
 * @see \Dfe\Moip\Facade\Preorder
 * @see \Df\StripeClone\Facade\Charge::needPreorder()
 * @method static Charge s(M $m)
 */
abstract class Preorder extends \Df\Payment\Facade {
	/**
	 * 2017-06-12
	 * @used-by \Df\StripeClone\Method::chargeNew()
	 * @see \Dfe\Moip\Facade\Preorder::create()
	 * @param array(string => mixed) $p
	 * @return object
	 */
	abstract function create(array $p);

	/**
	 * 2017-06-12
	 * @used-by \Df\StripeClone\Method::chargeNew()
	 * @see \Dfe\Moip\Facade\Preorder::id()
	 * @param object $o
	 * @return string
	 */
	abstract function id($o);
}