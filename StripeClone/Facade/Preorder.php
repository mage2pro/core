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
	 * 2022-11-12 `object` as a return type is not supported by PHP < 7.2: https://3v4l.org/dAmcs
	 * 2024-06-03 We need to support PHP ≥ 7.1.
	 * @used-by \Df\StripeClone\Method::chargeNew()
	 * @see \Dfe\Moip\Facade\Preorder::create()
	 * @param array(string => mixed) $p
	 * @return object
	 */
	abstract function create(array $p);

	/**
	 * 2017-06-12
	 * 2022-11-17
	 * `object` as an argument type is not supported by PHP < 7.2:
	 * https://github.com/mage2pro/core/issues/174#user-content-object
	 * @used-by \Df\StripeClone\Method::chargeNew()
	 * @see \Dfe\Moip\Facade\Preorder::id()
	 * @param object $o
	 */
	abstract function id($o):string;
}