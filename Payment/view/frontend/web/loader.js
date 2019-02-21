// 2016-08-24
define([
	'df', 'Magento_Checkout/js/model/payment/renderer-list', 'uiComponent'
   /**
	* 2018-09-22
	* «Mageplaza One Step Checkout does not show Mage2.PRO payment methods on the frontend checkout screen»
	* https://github.com/mage2pro/core/issues/78
	* 2019-02-22
	* «Make the Vantiv payment module compatible with a custom checkout module»
	* https://github.com/mage2pro/vantiv/issues/3
	* The previous code was:
	* 		,'Magento_Checkout/js/view/payment/list'
	* Now I have found a better solution which would probably solve all similar
	* third-party compatibility problems: https://github.com/mage2pro/vantiv/commit/a4a89710
	*/
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
	/** 2017-09-06 @uses Class::extend() https://github.com/magento/magento2/blob/2.2.0-rc2.3/app/code/Magento/Ui/view/base/web/js/lib/core/class.js#L106-L140 */	
	return Component.extend({});
}
);});