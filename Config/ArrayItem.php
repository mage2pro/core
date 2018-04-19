<?php
namespace Df\Config;
/**
 * 2017-01-24
 * @see \Dfe\AllPay\InstallmentSales\Plan\Entity
 * @see \Dfe\CurrencyFormat\O
 * @see \Doormall\Shipping\Partner\Entity
 */
abstract class ArrayItem extends O {
	/**
	 * 2015-12-31
	 * @used-by \Df\Config\A::get()
	 * @see \Dfe\AllPay\InstallmentSales\Plan\Entity::id()
	 * @see \Dfe\CurrencyFormat\O::id()
	 * @see \Doormall\Shipping\Partner\Entity::id()
	 * https://github.com/mage2pro/core/blob/dcc75ea95/Config/A.php?ts=4#L26
	 * 2017-01-24
	 * Решил не использовать @see \Df\Core\O::getId(),
	 * чтобы подчеркнуть, что класс — абстрактный.
	 * @return string
	 */
	abstract function id();

	/**
	 * 2016-08-07
	 * @used-by \Df\Config\Backend\ArrayT::processI()
	 * @see \Dfe\AllPay\InstallmentSales\Plan\Entity::sortWeight()
	 * @see \Dfe\CurrencyFormat\O::sortWeight()
	 * @see \Doormall\Shipping\Partner\Entity::sortWeight()
	 * @return int
	 */
	function sortWeight() {return 0;}
}