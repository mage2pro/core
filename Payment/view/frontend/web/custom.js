// 2016-07-18
define([
	'./mixin', 'df', 'Magento_Checkout/js/view/payment/default'
], function(mixin, df, parent) {'use strict'; return parent.extend(df.o.merge(mixin, {
	/**
	 * 2016-08-24
	 * @return {Object}
	*/
	initialize: function() {
		this._super();
		mixin.initialize.apply(this);
		return this;
	}
}));});


