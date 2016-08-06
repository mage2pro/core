// 2016-07-16
// 2016-08-06
// Используем $.extend вместо _.extend, потому что нам нужна опция deep:
// http://stackoverflow.com/a/24542665
// http://api.jquery.com/jquery.extend/
define([
	'Magento_Payment/js/view/payment/cc-form', './mixin', 'jquery'
], function(parent, mixin, $) {'use strict'; return parent.extend($.extend(true, {}, mixin, {
	defaults: {
		df: {
			card: {
				expirationMonth: 'expirationMonth'
				,expirationYear: 'expirationYear'
				,number: 'number'
				,verification: 'verification'
			},
			// 2016-08-06
			// @used-by mage2pro/core/Payment/view/frontend/web/template/item.html
			formTemplate: 'Df_Payment/bankCard'
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
	getCode: function() {return this.item.method;},
	/**
	 * 2016-08-06
	 * @override
	 * @see mage2pro/core/Payment/view/frontend/web/js/view/payment/mixin.js
	 * @used-by getData()
	 * @returns {Object}
	 */
	dfData: function() {return {token: this.token};},
	/**
	 * 2016-08-06
	 * @override
	 * @see mage2pro/core/Payment/view/frontend/web/js/view/payment/mixin.js
	 * @return {Boolean}
	*/
	validate: function() {
		/** @type {Boolean} */
		var result = !!this.selectedCardType();
		if (!result) {
			this.showErrorMessage('It looks like you have entered an incorrect bank card number.');
		}
		return result && this._super();
	}
}));});
