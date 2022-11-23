<?php
namespace Df\Customer\Plugin\Js;
use Magento\Customer\CustomerData\Customer as Sb;
/**
 * 2019-11-17
 * 2020-01-24
 * 1) How to get the current customer ID in JavaScript: https://magento.stackexchange.com/a/201284
 * 2) This plugin is intentionally disabled by default in vendor/mage2pro/core/Customer/etc/frontend/di.xml.
 * If you need the current customer's ID in JavaScript,
 * then enable the plugin in the `etc/frontend/di.xml` file of your module:
 *	<type name='Magento\Customer\CustomerData\Customer'>
 *		<plugin disabled='false' name='Df\Customer\Js\CustomerId' />
 *	</type>
 * Usages:
 * 1) https://github.com/mage2pro/sift/blob/0.0.3/etc/frontend/di.xml#L6-L10
 * https://github.com/mage2pro/sift/blob/0.0.3/view/frontend/web/main.js#L8
 * 2) https://github.com/justuno-com/m2/blob/1.2.3/etc/frontend/di.xml#L6-L10
 * https://github.com/justuno-com/m2/blob/1.2.3/view/frontend/web/main.js#L30
 */
final class CustomerId {
	/**
	 * 2019-11-17
	 * @see \Magento\Customer\CustomerData\Customer::getSectionData()
	 * @param array(string => mixed) $r
	 * @return array(string => mixed)
	 */
	function afterGetSectionData(Sb $sb, array $r):array {return ['id' => df_customer_id()] + $r;}
}