<?php
namespace Df\Config;
/**
 * 2017-01-24
 * @see \Dfe\AllPay\InstallmentSales\Plan\Entity
 * @see \Dfe\CurrencyFormat\O
 * @see \Dfe\Sift\PM\Entity
 * @see \Doormall\Shipping\Partner\Entity
 */
abstract class ArrayItem extends O {
	/**
	 * 2015-12-31
	 * @used-by df_hash_o()
	 * @used-by df_id()
	 * @used-by \Df\Config\A::get()
	 * @see \Dfe\AllPay\InstallmentSales\Plan\Entity::id()
	 * @see \Dfe\CurrencyFormat\O::id()
	 * @see \Dfe\Sift\PM\Entity::id()
	 * @see \Doormall\Shipping\Partner\Entity::id()
	 * https://github.com/mage2pro/core/blob/dcc75ea95/Config/A.php?ts=4#L26
	 */
	abstract function id():string;

	/**
	 * 2016-08-07
	 * @used-by \Df\Config\Backend\ArrayT::processI()
	 * @see \Dfe\AllPay\InstallmentSales\Plan\Entity::sortWeight()
	 * @see \Dfe\CurrencyFormat\O::sortWeight()
	 * @see \Doormall\Shipping\Partner\Entity::sortWeight()
	 */
	function sortWeight():int {return 0;}
}