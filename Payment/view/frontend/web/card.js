// 2016-07-16
define([
	'./mixin', 'df', 'jquery', 'ko', 'Magento_Payment/js/view/payment/cc-form'
], function(mixin, df, $, ko, parent) {'use strict'; return parent.extend(df.o.merge(mixin, {
	defaults: {
		df: {
			card: {
				expirationMonth: 'expirationMonth'
				,expirationYear: 'expirationYear'
				// 2016-09-28
				// @used-by mage2pro/core/Payment/view/frontend/web/template/card.html
				,newTemplate: 'Df_Payment/card/new'
				,number: 'number'
				/**
				 * 2016-11-10
				 * @used-by prefill()
				 */
				,prefill: {cvv: 111}
				,verification: 'verification'
			},
			// 2016-08-06
			// @used-by mage2pro/core/Payment/view/frontend/web/template/item.html
			formTemplate: 'Df_Payment/card'
		}
	},
	dfCardExpirationMonth: function() {return this.dfInputValueByData(this.df.card.expirationMonth);},
	dfCardExpirationMonthId: function() {return this.fid('expiration');},
	dfCardExpirationYear: function() {return this.dfInputValueByData(this.df.card.expirationYear);},
	dfCardExpirationYearId: function() {return this.fid('expiration_yr');},
	dfCardNumber: function() {return this.dfInputValueByData(this.df.card.number);},
	dfCardNumberId: function() {return this.fid('cc_number');},
	dfCardVerification: function() {return this.dfInputValueByData(this.df.card.verification);},
	dfCardVerificationId: function() {return this.fid('cc_cid');},
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
	 * @see mage2pro/core/Payment/view/frontend/web/mixin.js
	 * @used-by getData()
	 * @returns {Object}
	 */
	dfData: function() {return {token: this.token};},
	/**
	 * 2016-08-16
	 * @override
	 * @see mage2pro/core/Payment/view/frontend/web/mixin.js
	 * @used-by dfFormCssClassesS()
	 * @returns {String[]}
	 */
	dfFormCssClasses: function() {return mixin.dfFormCssClasses.call(this).concat(['df-card']);},
	/**
	 * 2016-09-28
	 * @param {String} id
	 * @returns {String}
	 */
	fid: function(id) {return this.getCode() + '_' + id;},
	/**
	 * 2016-08-23
	 * @return {Object}
	*/
	initialize: function() {
		this._super();
		mixin.initialize.apply(this);
		this.savedCards = this.config('savedCards');
		this.hasSavedCards = !!this.savedCards.length;
		this.newCardId = 'new';
		this.currentCard = ko.observable(!this.hasSavedCards ? this.newCardId : this.savedCards[0].id);
		this.isNewCardChosen = ko.computed(function() {
			return this.newCardId === this.currentCard();
		}, this);
		// 2016-11-10
		// Prefill should work only in the Test mode.
		if (this.isTest()) {
			// 2016-11-10
			// «Prefill the Payment Form with Test Data?»			
			/** @type {*} */
			var prefill = this.config('prefill');
			if (prefill) {
				this.prefill(prefill);				
			}
		}		
		return this;
	},
	/**
	 * 2016-11-10
	 * @used-by initialize()
	 * @param {*} d
	 */
	prefill: function(d) {
		if (d) {
			this.creditCardNumber(d);
			this.prefillWithAFutureData();
			this.creditCardVerificationNumber(this.df.card.prefill.cvv);
		}
	},	
	/**
	 * 2016-08-26
	 * http://stackoverflow.com/a/6002276
	 * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Date/getFullYear
	 */
	prefillWithAFutureData: function() {
		this.creditCardExpMonth(7);
		this.creditCardExpYear(1 + new Date().getFullYear());
	},
	/**
	 * 2016-08-06
	 * @override
	 * @see mage2pro/core/Payment/view/frontend/web/mixin.js
	 * @return {Boolean}
	*/
	validate: function() {
		/** @type {Boolean} */
		var result = !this.isNewCardChosen() || !!this.selectedCardType();
		if (!result) {
			this.showErrorMessage('It looks like you have entered an incorrect bank card number.');
		}
		return result && this._super();
	}
}));});
