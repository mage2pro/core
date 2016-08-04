// 2016-07-16
define([
	'Magento_Payment/js/view/payment/cc-form', './mixin', 'underscore'
], function(Component, mixin, _) {return Component.extend(_.extend(mixin, {
	defaults: {template: 'Df_Payment/bankCard'},
	df: {
		card: {
			expirationMonth: 'expirationMonth'
			,expirationYear: 'expirationYear'
			,number: 'number'
			,verification: 'verification'
		}
	},
	dfCardExpirationMonth: function() {return this.dfForm(this.df.card.expirationMonth);},
	dfCardExpirationYear: function() {return this.dfForm(this.df.card.expirationYear);},
	dfCardNumber: function() {return this.dfForm(this.df.card.number);},
	dfCardVerification: function() {return this.dfForm(this.df.card.verification);},
	/**
	 * 2016-08-04
	 * @override
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Payment/view/frontend/web/js/view/payment/cc-form.js#L98-L104
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/view/payment/default.js#L203-L208
	 * @returns {String}
	 */
	getCode: function() {return this.item.method;}
}))});
