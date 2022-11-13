<?php
namespace Df\Theme\Controller\Result;
use Magento\Theme\Controller\Result\MessagePlugin as _P;
/**
 * 2021-04-18
 * 1) "Fix the «Unable to send the cookie. Size of 'mage-messages' is <…> bytes» Magento core bug":
 * https://github.com/mage2pro/core/issues/153
 * 2) @see \Magento\Theme\Controller\Result\MessagePlugin was introduced in Magento 2.0.8:
 * https://github.com/magento/magento2/blob/2.0.8/app/code/Magento/Theme/Controller/Result/MessagePlugin.php
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 */
class MessagePlugin extends _P {
	/**
	 * 2021-04-18
	 * @override
	 * @see \Magento\Theme\Controller\Result\MessagePlugin::getCookiesMessages()
	 * @used-by \Magento\Theme\Controller\Result\MessagePlugin::getMessages()
	 */
	protected function getCookiesMessages():array {return array_slice(
		# 2021-06-04
		# "«Unable to unserialize value» on the `sales/guest/view` page":
		# https://github.com/canadasatellite-ca/site/issues/139
		df_try(function() {return parent::getCookiesMessages();}, []), 0, 10
	);}
}