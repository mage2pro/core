<?php
namespace Df\Customer\Plugin;
use Magento\Customer\CustomerData\Customer as Sb;
use Magento\Customer\Helper\Session\CurrentCustomer as C;
/**
 * 2019-11-17
 * 2020-01-24
 * 1) How to get the current customer ID in JavaScript: https://magento.stackexchange.com/a/201284
 * This plugin is intentionally disabled by default in vendor/mage2pro/core/Customer/etc/frontend/di.xml.
 * If you need the current customer's ID in JavaScript,
 * then enable the plugin in the `etc/frontend/di.xml` file of your module:
 *	<type name='Magento\Customer\CustomerData\Customer'>
 *		<plugin disabled='false' name='Df\Customer\GetCustomerIdInJS' />
 *	</type>
 */
final class GetCustomerIdInJS {
	/**
	 * 2019-11-17
	 * @see \Magento\Customer\CustomerData\Customer::getSectionData()
	 * @param Sb $sb
	 * @param array(string => mixed) $r
	 * @return array(string => mixed)
	 */
	function afterGetSectionData(Sb $sb, array $r) {
		$c = df_o(C::class); /** @var C $c */
		return ['id' => $c->getCustomerId()] + $r;
	}
}