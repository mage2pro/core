// 2016-08-24
define([
	'df', 'Magento_Checkout/js/model/payment/renderer-list', 'uiComponent'
], function(df, rendererList, Component) {'use strict'; return (
	/**
	 * @param {String} name
	 * @param {?String} code
	 * @returns {Component}
	 */
	function(name, code) {
		/**
		 * 2016-08-24
		 * 'Dfe_AllPay' => 'dfe_all_pay'
		 * @type {String}
		 */
		code = code || df.s.lcFirst(df.s.explodeModuleCamel(name)).join('_');
		if (window.checkoutConfig.payment[code]) {
			rendererList.push({type: code, component: name + '/main'});
		}
		return Component.extend({});
	}
);});
