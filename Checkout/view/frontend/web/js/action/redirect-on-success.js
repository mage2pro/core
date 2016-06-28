/**
 * 2016-06-28
 * Файл https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/action/redirect-on-success.js
 * отсутствует в версиях ранее 2.1.0: https://github.com/CKOTech/checkout-magento2-plugin/issues/3
 * Поэтому эмулируем его.
 */
define(['mage/url'], function (url) {
	'use strict';
	/** @type {String} */
	var redirectUrl = window.checkoutConfig.defaultSuccessPageUrl;
	/**
	 * 2016-06-28
	 * window.checkoutConfig.defaultSuccessPageUrl отсутствует в версиях ранее 2.1.0:
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/Model/DefaultConfigProvider.php#L268
	 */
	if (!redirectUrl) {
		/**
		 * 2016-06-28
		 * По аналогии с https://github.com/magento/magento2/blob/2.0.7/app/code/Magento/Checkout/view/frontend/web/js/action/place-order.js#L51
		 */
		redirectUrl = 'checkout/onepage/success/';
	}
	return {
		redirectUrl: redirectUrl
		,execute: function () {window.location.replace(url.build(this.redirectUrl));}
	};
});
