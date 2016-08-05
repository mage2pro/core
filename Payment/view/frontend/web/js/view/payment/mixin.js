// 2016-08-04
define ([
	'./createMessagesComponent'
	, 'df'
	, 'jquery'
	, 'mage/translate'
	, 'Magento_Checkout/js/model/payment/additional-validators'
], function(createMessagesComponent, df, $, $t, validators) {return {
	/**
	 * 2016-08-04
	 * @param {?String} key
	 * @returns {Object}|{*}
	 */
	config: function(key) {
		/** @type {Object} */
		var result =  window.checkoutConfig.payment[this.getCode()];
		return !key ? result : result[key];
	},
	createMessagesComponent: createMessagesComponent,
	/**
	 * 2016-08-04
	 * @return {String}
	*/
	getDebugMessage: function() {return '';},
	defaults: {active: false},
	/**
	 * 2016-08-04
	 * @param {?String} field [optional]
	 * @returns {jQuery}|{String}
	 */
	dfForm: function(field) {
		if (df.undefined(this._dfForm)) {
			this._dfForm = $('form.' + this.getCode());
		}
		return !field ? this._dfForm : $('[data="' + field + '"]', this._dfForm).val();
	},
	imports: {onActiveChange: 'active'},
	/**
	 * 2016-08-04
	 * @return {Boolean}
	*/
	isTest: function() {return this.config('isTest');},
	/**
	 * 2016-08-05
	 * @param {String} message
	 * @param {?Object} params [optional]
	 * @param {?Boolean} needTranslate [optional]
	*/
	showErrorMessage: function(message, params, needTranslate) {
		message = !df.arg(needTranslate, true) ? message : $t(message);
		$.each(df.arg(params, {}), function(name, value) {
			message = message.replace(name, value);
		});
		this.messageContainer.addErrorMessage({'message': message});
	},
	/**
	 * 2016-08-05
	 * @override
	 * @see https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/view/payment/default.js#L210-L215
	 * @return {Boolean}
	*/
	validate: function() {
		/**
		 * 2016-08-05
		 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/model/payment/additional-validators.js#L29-L48
		 * It validates:
		 * 1) the customer's email (if the customer is not logged in): https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/model/customer-email-validator.js#L23-L33
		 * 2) the checkout agreement acceptance: https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/CheckoutAgreements/view/frontend/web/js/model/agreement-validator.js#L20-L46
		 *
		 * The inherited this.validate() method actually does nothing and just returns true:
		 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/view/payment/default.js#L210-L215
		 */
		return this._super() && validators.validate();
	}
};});
