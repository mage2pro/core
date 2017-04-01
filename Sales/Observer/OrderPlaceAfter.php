<?php
namespace Df\Sales\Observer;
use Magento\Framework\Event\Observer as O;
use Magento\Framework\Event\ObserverInterface;
// 2017-04-01
final class OrderPlaceAfter implements ObserverInterface {
	/**
	 * @override
	 * @see ObserverInterface::execute()
	 * What events are triggered on an order placement? https://mage2.pro/t/3573
	 * @param O $o
	 */
	function execute(O $o) {df_modules_log();}
}