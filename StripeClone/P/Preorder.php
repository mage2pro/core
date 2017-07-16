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
	 * @used-by request()
	 * @see \Dfe\Moip\P\Preorder::p()
	 * @return array(string => mixed)
	 */
	abstract protected function p();

	/**
	 * 2017-06-12
	 * @used-by \Df\StripeClone\Method::chargeNew()
	 * @param M $m
	 * @return array(string => mixed)
	 */
	final static function request(M $m) {
		$i = df_new(df_con_heir($m, __CLASS__), $m); /** @var self $i */
		return $i->p();
	}
}