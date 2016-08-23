<?php
namespace Df\Customer\Observer\CopyFieldset;
use Df\Customer\Setup\UpgradeSchema as Schema;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer as O;
use Magento\Framework\Event\ObserverInterface;
/**
 * 2016-08-22
 * Событие: core_copy_fieldset_order_address_to_customer
 * @see \Magento\Framework\DataObject\Copy::dispatchCopyFieldSetEvent()
 * https://mage2.pro/t/1975
 */
class OrderAddressToCustomer implements ObserverInterface {
	/**
	 * 2016-08-22
	 * @override
	 * @see ObserverInterface::execute()
	 * @used-by \Magento\Framework\Event\Invoker\InvokerDefault::_callObserverMethod()
	 * @param O $o
	 * @return void
	 */
	public function execute(O $o) {
		df_customer_info_add($o['target'], df_checkout_session()->getDfCustomer() ?: []);
	}
}

