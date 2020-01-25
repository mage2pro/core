<?php
namespace Df\Customer\Plugin\Js;
use Magento\Customer\CustomerData\Customer as Sb;
/**
 * 2020-01-26
 * This plugin is intentionally disabled by default in vendor/mage2pro/core/Customer/etc/frontend/di.xml.
 * If you need the current customer session ID in JavaScript,
 * then enable the plugin in the `etc/frontend/di.xml` file of your module:
 *	<type name='Magento\Customer\CustomerData\Customer'>
 *		<plugin disabled='false' name='Df\Customer\Js\SessionId' />
 *	</type>
 * An usage:
 * https://github.com/mage2pro/sift/blob/0.0.5/etc/frontend/di.xml#L8
 * https://github.com/mage2pro/sift/blob/0.0.5/view/frontend/web/main.js#
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