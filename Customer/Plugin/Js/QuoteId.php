<?php
namespace Df\Customer\Plugin\Js;
use Magento\Customer\CustomerData\Customer as Sb;
use Magento\Customer\Helper\Session\CurrentCustomer as C;
/**
 * 2019-11-17
 * 2020-01-24
 * This plugin is intentionally disabled by default in vendor/mage2pro/core/Customer/etc/frontend/di.xml.
 * If you need the current quote ID in JavaScript,
 * then enable the plugin in the `etc/frontend/di.xml` file of your module:
 *	<type name='Magento\Customer\CustomerData\Customer'>
 *		<plugin disabled='false' name='Df\Customer\Js\QuoteId' />
 *	</type>
 * Usages:
 * 1) https://github.com/mage2pro/sift/blob/0.0.4/etc/frontend/di.xml#L9
 * https://github.com/mage2pro/sift/blob/0.0.4/view/frontend/web/main.js#L7
 */
final class QuoteId {
	/**
	 * 2019-11-17
	 * @see \Magento\Customer\CustomerData\Customer::getSectionData()
	 * @param Sb $sb
	 * @param array(string => mixed) $r
	 * @return array(string => mixed)
	 */
	function afterGetSectionData(Sb $sb, array $r) {return ['quoteId' => df_quote_id()] + $r;}
}