// 2016-08-24
define([
	'df', 'Magento_Checkout/js/model/payment/renderer-list', 'uiComponent'
], function(df, rendererList, Component) {'use strict'; return (
/**
 * 2016-08-24
 * @param {String} name
 * @param {String=} code
 * @returns {Component}
 */
function(name, code) {
	// 2016-08-24 'Dfe_AllPay' => 'dfe_all_pay'
	/** @type {String} */ code = code || df.s.lcFirst(df.s.explodeModuleCamel(name)).join('_');
	if (window.checkoutConfig.payment[code]) {
		// 2017-07-24
		// `rendererList` is an «observable array»: http://knockoutjs.com/documentation/observableArrays.html
		// https://github.com/magento/magento2/blob/2.2.0-RC1.5/app/code/Magento/Checkout/view/frontend/web/js/model/payment/renderer-list.js#L11
		rendererList.push({type: code, component: name + '/main'});
	}
	return Component.extend({});
}
);});