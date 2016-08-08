// 2016-07-16
define([
	'./mixin', 'df', 'Magento_Payment/js/view/payment/cc-form'
], function(mixin, df, parent) {'use strict'; return parent.extend(df.o.merge(mixin, {
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
	dfCardExpirationMonth: function() {return this.dfInputValueByData(this.df.card.expirationMonth);},
	dfCardExpirationYear: function() {return this.dfInputValueByData(this.df.card.expirationYear);},
	dfCardNumber: function() {return this.dfInputValueByData(this.df.card.number);},
	dfCardVerification: function() {return this.dfInputValueByData(this.df.card.verification);},
	/**
	 * 2016-08-04
	 * @param {String} value
	 * @returns {String}
	 */
	dfInputValueByData: function(value) {return this.dfFormElementByAttr('data', value).val();},
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
