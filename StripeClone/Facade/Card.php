<?php
namespace Df\StripeClone\Facade;
// 2017-01-11
final class Card {
	/**
	 * 2017-01-11
	 * @used-by \Df\StripeClone\Block\Info::prepare()
	 * @used-by \Df\StripeClone\Facade\Charge::card() 
	 * @used-by \Df\StripeClone\Facade\Customer::cards()
	 * @param string|object $m
	 * @param object|array(string => string) $data
	 * @return ICard
	 */
	static function create($m, $data) {return df_new(df_con_heir($m, ICard::class), $data);}
}