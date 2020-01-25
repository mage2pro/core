<?php
namespace Df\Customer\Plugin\Js;
use Magento\Customer\CustomerData\Customer as Sb;
use Magento\Customer\Helper\Session\CurrentCustomer as C;
/**
 * 2020-01-26
 * This plugin is intentionally disabled by default in vendor/mage2pro/core/Customer/etc/frontend/di.xml.
 * If you need the current customer session ID in JavaScript,
 * then enable the plugin in the `etc/frontend/di.xml` file of your module:
 *	<type name='Magento\Customer\CustomerData\Customer'>
 *		<plugin disabled='false' name='Df\Customer\Js\SessionId' />
 *	</type>
 */
final class SessionId {
	/**
	 * 2020-01-26
	 * @see \Magento\Customer\CustomerData\Customer::getSectionData()
	 * @param Sb $sb
	 * @param array(string => mixed) $r
	 * @return array(string => mixed)
	 */
	function afterGetSectionData(Sb $sb, array $r) {return ['sessionId' => df_customer_session_id()] + $r;}
}