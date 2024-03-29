<?php
namespace Df\StripeClone\P;
use Df\StripeClone\Method as M;
/**
 * 2017-06-12
 * @see \Dfe\Moip\P\Preorder
 * @method M m()
 */
abstract class Preorder extends \Df\Payment\Operation {
	/**
	 * 2017-06-12
	 * @used-by self::request()
	 * @see \Dfe\Moip\P\Preorder::p()
	 * @return array(string => mixed)
	 */
	abstract protected function p():array;

	/**
	 * 2017-06-12
	 * @used-by \Df\StripeClone\Method::chargeNew()
	 * @return array(string => mixed)
	 */
	final static function request(M $m):array {
		$i = df_new(df_con_heir($m, __CLASS__), $m); /** @var self $i */
		return $i->p();
	}
}