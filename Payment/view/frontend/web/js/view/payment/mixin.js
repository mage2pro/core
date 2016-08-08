// 2016-08-04
define ([
	'./createMessagesComponent'
	,'df'
	,'Df_Checkout/js/action/place-order'
	,'Df_Checkout/js/action/redirect-on-success'
	,'Df_Checkout/js/data'
	,'jquery'
	,'mage/translate'
	,'Magento_Checkout/js/model/payment/additional-validators'
], function(
	createMessagesComponent
	, df
	, placeOrderAction
	, redirectOnSuccessAction
	, dfc
	, $, $t, validators
) {'use strict'; return {
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
	defaults: {
		active: false
		,df: {
			askForBillingAddress: true,
			// 2016-08-06
			// @used-by mage2pro/core/Payment/view/frontend/web/template/item.html
			formTemplate: null,
			test: {
				showBackendTitle: true
				,suffix: 'TEST MODE'
			}
		},
		template: 'Df_Payment/item'
	},
	dfc: dfc,
	/**
	 * 2016-08-06
	 * @used-by getData()
	 * @returns {Object}
	 */
	dfData: function() {return {};},
	/**
	 * 2016-08-04
	 * @param {?String} field [optional]
	 * @returns {jQuery}|{String}
	 */
	dfForm: function(field) {
		if (df.u(this._dfForm)) {
			this._dfForm = $('form.' + this.getCode());
		}
		return !field ? this._dfForm : $('[data="' + field + '"]', this._dfForm).val();
	},
	/**
	 * 2016-08-06
	 * @override
	 * @see https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/view/payment/default.js#L185-L194
	 * @see https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Payment/view/frontend/web/js/view/payment/cc-form.js#L106-L124
	 * @used-by placeOrderInternal()
	 * @used-by getPlaceOrderDeferredObject(): https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/view/payment/default.js#L161-L165
	 * @used-by selectPaymentMethod(): https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/view/payment/default.js#L167-L175
	 */
	getData: function () {
		return {
			/**
			 * 2016-05-03
			 * Если не засунуть данные (например, «token») внутрь «additional_data»,
			 * то получим сбой типа:
			 * «Property "Token" does not have corresponding setter
			 * in class "Magento\Quote\Api\Data\PaymentInterface»
			 */
			additional_data: this.dfData()
			,method: this.item.method
		};
	},
	/**
	 * 2016-08-04
	 * @return {String}
	*/
	getDebugMessage: function() {return '';},
	/**
	 * 2016-07-01
	 * @return {String}
	*/
	getTitle: function() {
		return df.a.ccClean(' ', [this._super(), !this.isTest() ? null :
			'[<b>title</b>]'.replace('title', df.a.ccClean(' ', [
				this.df.test.showBackendTitle ? this.config('titleBackend') : null
				, this.df.test.suffix
			]))
		]);
	},
	imports: {onActiveChange: 'active'},
	/**
	 * 2016-08-04
	 * @return {Boolean}
	*/
	isTest: function() {return this.config('isTest');},
	/**
	 * 2016-08-06
	 * @used-by placeOrderInternal()
	 */
	onSuccess: function() {redirectOnSuccessAction.execute()},
	/** 2016-08-06 */
	placeOrderInternal: function() {
		var _this = this;
		$.when(placeOrderAction(this.getData(), this.messageContainer, this.config('route')))
			.fail(function() {_this.isPlaceOrderActionAllowed(true);})
			.done(this.onSuccess)
		;
	},
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
