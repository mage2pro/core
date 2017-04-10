// 2016-07-18
// @see Df_Payment/withOptions
// https://github.com/mage2pro/core/blob/2.0.29/Payment/view/frontend/web/withOptions.js?ts=4
define([
	'./mixin', 'df', 'Magento_Checkout/js/view/payment/default'
], function(mixin, df, parent) {'use strict'; return parent.extend(df.o.merge(mixin, {
	/**
	 * 2016-08-24
	 * @override
	 * @see Magento_Checkout/js/view/payment/default::initialize()
	 * https://github.com/magento/magento2/blob/2.1.5/app/code/Magento/Checkout/view/frontend/web/js/view/payment/default.js#L53-L91
	 * @returns {Object}
	 * @see Df_Payment/withOptions::initialize()
	*/
	initialize: function() {
		this._super();
		mixin.initialize.apply(this);
		return this;
	},
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
}));});


