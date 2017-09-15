<?php
namespace Df\Framework\Form\Element;
/**
 * 2016-05-30
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 * @used-by https://github.com/mage2pro/2checkout/blob/1.4.5/etc/adminhtml/system.xml#L318
 * @used-by https://github.com/mage2pro/checkout.com/blob/1.3.11/etc/adminhtml/system.xml#L165
 * @used-by https://github.com/mage2pro/dragonpay/blob/1.1.0/etc/adminhtml/system.xml#L115
 * @used-by https://github.com/mage2pro/omise/blob/1.8.8/etc/adminhtml/system.xml#L205
 * @used-by https://github.com/mage2pro/paymill/blob/1.4.6/etc/adminhtml/system.xml#L190
 * @used-by https://github.com/mage2pro/postfinance/blob/1.0.5/etc/adminhtml/system.xml#L208 
 * @used-by https://github.com/mage2pro/qiwi/blob/1.0.0/etc/adminhtml/system.xml#L109
 * @used-by https://github.com/mage2pro/robokassa/blob/0.0.2/etc/adminhtml/system.xml#L163
 * @used-by https://github.com/mage2pro/square/blob/1.1.7/etc/adminhtml/system.xml#L189
 * @used-by https://github.com/mage2pro/stripe/blob/1.9.11/etc/adminhtml/system.xml#L154
 * @used-by https://github.com/mage2pro/yandex-kassa/blob/0.0.6/etc/adminhtml/system.xml#L99
 * @used-by https://github.com/mage2pro/yandex-kassa/blob/0.0.6/etc/adminhtml/system.xml#L112
 */
class Webhook extends Url {
	/**
	 * 2016-05-31
	 * @override
	 * @see \Df\Framework\Form\Element\Url::messageForThirdPartyLocalhost()
	 * @used-by \Df\Framework\Form\Element\Url::getElementHtml()
	 * @return string
	 */
	final protected function messageForThirdPartyLocalhost() {return
		'The notifications are not available, because the store is running on <b>localhost</b>'
	;}
}