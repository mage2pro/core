// 2017-02-07
// 2017-02-05
define([
	'Df_Payment/card'
], function(parent) {'use strict'; return parent.extend({
   /**
	* 2017-02-07
	* @returns {String}
 	*/
	publicKey: function() {return this.config('publicKey');},
	
	/**
	 * 2017-02-16
	 * @final
	 * @override
	 * @see https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/view/payment/default.js#L127-L159
	 * @used-by https://github.com/magento/magento2/blob/2.1.0/lib/web/knockoutjs/knockout.js#L3863
	 * @param {this} _this
	*/
	placeOrder: function(_this) {
		if (this.validate()) {
			if (!this.isNewCardChosen()) {
				this.token = this.currentCard();
				this.placeOrderInternal();
			}
			else {
				this.createToken();
				// 2017-02-06
				// https://developers.paymill.com/guides/reference/transactions#direct-tokenization
				paymill.createToken(
					{
						// 2017-02-06
						// https://blog.paymill.com/en/clarification-on-amount-int/#content-wrapper
						// 2017-02-07
						// Whether all the Paymill-supported currencies are 2-decimal
						// or some currencies are zero-decimal?
						// https://mage2.pro/t/2675
						amount_int: this.amountF()
						,cardholder: this.cardholder()
						,currency: this.paymentCurrency().code
						,cvc: this.creditCardVerificationNumber()
						,exp_month: this.creditCardExpMonth()
						,exp_year: this.creditCardExpYear()
						,number: this.creditCardNumber()
					},
					/**
					 * 2017-02-06
					 * @param {?Object} error
					 * При успешном получении токена error равно null.
					 * @param {Object} response
					 * Пример response при успешном получении токена:
					 * {
							bin: "401288"
							,binCountry: "DE"
							,brand: "VISA"
							,ip: "80.147.111.188"
							,ipCountry: ""
							,last4Digits: "1881"
							,token: "tok_48b61d2a802477e42dde2ad9874e"
					 * }
					 * 2017-02-08
					 * [Paymill] The «paymill.createToken» call for the «5105105105105100» card
					 * with the «07/2021» expiration data should fail in the test mode
					 * according to the documentation, but it is not:
					 * https://mage2.pro/t/2677
					 */
					function(error, response) {
						if (!error) {
							_this.token = response.token;
							_this.placeOrderInternal();
						}
						else {
							// 2017-02-08
							// https://developers.paymill.com/guides/reference/bridge#2-creating-a-token
							_this.showErrorMessage(error.message);
						}
					}
				);
			}
		}
	},	
});});