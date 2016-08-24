// 2016-08-24
define([
	'df', 'Magento_Checkout/js/model/payment/renderer-list', 'uiComponent'
], function(df, rendererList, Component) {'use strict'; return (
	/**
	 * @param {String} name
	 * @returns {Component}
	 */
	function(name) {
		/**
		 * 2016-08-24
		 * 'Dfe_AllPay' => 'dfe_all_pay'
		 * @type {String}
		 */
		var code = df.s.lcFirst(df.s.explodeModuleCamel(name)).join('_');
		if (window.checkoutConfig.payment[code]) {
			rendererList.push({type: code, component: name + '/main'});
		}
		return Component.extend({});
	}
);});
