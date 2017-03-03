// 2016-07-18
// @see Df_Payment/withOptions
// https://github.com/mage2pro/core/blob/2.0.29/Payment/view/frontend/web/withOptions.js?ts=4
define([
	'./mixin', 'df', 'Magento_Checkout/js/view/payment/default'
], function(mixin, df, parent) {'use strict'; return parent.extend(df.o.merge(mixin, {
	/**
	 * 2016-08-24
	 * @returns {Object}
	*/
	initialize: function() {
		this._super();
		mixin.initialize.apply(this);
		return this;
	}
}));});


