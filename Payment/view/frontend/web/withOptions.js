// 2017-03-03
// @see Df_GingerPaymentsBase/main
// https://github.com/mage2pro/ginger-payments-base/blob/0.1.1/view/frontend/web/main.js?ts=4
// @see Dfe_AllPay/main
// https://github.com/mage2pro/allpay/blob/1.1.37/view/frontend/web/main.js?ts=4
define([
	'df', 'Df_Core/my/redirectWithPost', 'Df_Payment/custom', 'jquery'
], function(df, redirectWithPost, parent, $) {'use strict'; return parent.extend({
	defaults: {df: {test: {showBackendTitle: false}}},
	/**
	 * 2016-08-08
	 * 2017-03-01
	 * Задаёт набор передаваемых на сервер при нажатии кнопки «Place Order» данных.
	 * @override
	 * @see mage2pro/core/Payment/view/frontend/web/mixin.js::dfData()
	 * @used-by mage2pro/core/Payment/view/frontend/web/mixin.js::getData()
	 * https://github.com/mage2pro/core/blob/2.0.21/Payment/view/frontend/web/mixin.js?ts=4#L208-L225
	 * @returns {Object}
	 */
	dfData: function() {return df.o.merge(this._super(), df.clean({
		// 2017-03-01
		// @see \Dfe\AllPay\Method::II_OPTION
		// https://github.com/mage2pro/allpay/blob/1.1.32/Method.php?ts=4#L126
		option: this.option
	}));},
	/**
	 * 2016-08-06
	 * @override
	 * @see mage2pro/core/Payment/view/frontend/web/mixin.js
	 * @used-by placeOrderInternal()
	 */
	onSuccess: function(json) {
		/** @type {Object} */
		var data = $.parseJSON(json);
		// 2016-07-10
		// @see \Dfe\AllPay\Method::getConfigPaymentAction()
		redirectWithPost(data.uri, data.params);
	},
	/**
	 * 2016-08-15
	 * @returns {Object}
	 */
	options: function() {return this.config('options');},
	/**
	 * 2016-08-15
	 * @returns {Object[]}
	 */
	optionsA: function() {var _this = this; return $.map(this.options(), function(label, v) {return {
		// 2017-03-04
		// @used-by Df_Payment/withOptions
		// https://github.com/mage2pro/core/blob/2.0.31/Payment/view/frontend/web/template/withOptions.html?ts=4#L10-L11
		domId: [_this.getCode(), 'option', v].join('-'), label: label, value: v
	};});}
});});
