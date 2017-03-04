// 2017-03-03
// @see Df_GingerPaymentsBase/main
// https://github.com/mage2pro/ginger-payments-base/blob/0.1.1/view/frontend/web/main.js?ts=4
// @see Dfe_AllPay/main
// https://github.com/mage2pro/allpay/blob/1.1.37/view/frontend/web/main.js?ts=4
define([
	'df', 'Df_Core/my/redirectWithPost', 'Df_Payment/custom', 'jquery','ko'
], function(df, redirectWithPost, parent, $, ko) {'use strict'; return parent.extend({
	defaults: {df: {test: {showBackendTitle: false}}},
	/**
	 * 2016-08-08
	 * 2017-03-01
	 * Задаёт набор передаваемых на сервер при нажатии кнопки «Place Order» данных.
	 * @override
	 * @see mage2pro/core/Payment/view/frontend/web/mixin.js::dfData()
	 * @used-by mage2pro/core/Payment/view/frontend/web/mixin.js::getData()
	 * https://github.com/mage2pro/core/blob/2.0.21/Payment/view/frontend/web/mixin.js?ts=4#L208-L225
	 * @see \Dfe\AllPay\Method::II_OPTION
	 * https://github.com/mage2pro/allpay/blob/1.1.32/Method.php?ts=4#L126
	 * @returns {Object}
	 */
	dfData: function() {return df.o.merge(this._super(), df.clean({
		option: this.postProcessOption(this.option())
	}));},
	/**
	 * 2017-03-04
	 * @returns {Object}
	*/
	initialize: function() {
		this._super();
		this.option = ko.observable();
		return this;
	},
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
	 * @final
	 * @used-by optionsA()
	 * @used-by Dfe_AllPay/main::oneOffOptions()
	 * https://github.com/mage2pro/allpay/blob/1.1.40/view/frontend/web/main.js?ts=4#L103
	 * @returns {Object}
	 */
	options: function() {return this.config('options');},
	/**
	 * 2016-08-15
	 * @returns {Object[]}
	 */
	optionsA: function() {var _this = this; return $.map(this.options(), function(v, k) {return {
		// 2017-03-04
		// @used-by Df_Payment/withOptions
		// https://github.com/mage2pro/core/blob/2.0.31/Payment/view/frontend/web/template/withOptions.html?ts=4#L10-L11
		domId: [_this.getCode(), 'option', v].join('-'), label: v, value: k
	};});},
	/**
	 * 2017-03-04
	 * Allows to add a control after an option.
	 * @see Df_Payment/null
	 * https://github.com/mage2pro/core/blob/2.0.35/Payment/view/frontend/web/template/null.html
	 * @used-by Df_Payment/withOptions
	 * https://github.com/mage2pro/core/blob/2.0.35/Payment/view/frontend/web/template/withOptions.html?ts=4#L20
	 * @see Df_GingerPaymentsBase/main::optionAfter()
	 * https://github.com/mage2pro/ginger-payments-base/blob/0.1.9/view/frontend/web/main.js?ts=4#L42-L52
	 * @param {String} v
	 * @returns {String}
	 */
	optionAfter: function(v) {return 'Df_Payment/null';},
	/**
	 * 2016-07-01
	 * @override
	 * @see https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/view/payment/default.js#L127-L159
	 * @used-by https://github.com/magento/magento2/blob/2.1.0/lib/web/knockoutjs/knockout.js#L3863
	*/
	placeOrder: function() {
		if (this.validate()) {
			this.placeOrderInternal();
		}
	},
	/**
	 * 2017-03-05
	 * @used-by dfData()
	 * @param {String} option
	 * @returns {?String}
	 */
	postProcessOption: function(option) {return option;}
});});
