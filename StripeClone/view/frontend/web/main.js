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
	 * @abstract
	 * @used-by placeOrder()
	 * @param {Object|Number} status
	 * @returns {Boolean}
	 */
	tokenCheckStatus: function(status) {return null;},

    /**
	 * 2017-02-16
	 * @abstract
	 * @used-by placeOrder()
	 * @param {Object} params
	 * @param {Function} callback
	 * @returns {Function}
	 */
	tokenCreate: function(params, callback) {return null;},
	
    /**
	 * 2017-02-16
	 * @abstract
	 * @used-by placeOrder()
	 * @param {Object|Number} status
	 * @param {Object} resp
	 * @returns {String}
	 */
	tokenErrorMessage: function(status, resp) {return null;},
	
    /**
	 * 2017-02-16
	 * @abstract
	 * @used-by placeOrder()
	 * @param {Object} resp
	 * @returns {String}
	 */
	tokenFromResponse: function(resp) {return null;},	

    /**
	 * 2017-02-16
	 * @abstract
	 * @used-by placeOrder()
	 * @returns {Object}
	 */
	tokenParams: function() {return null;},
	
	/**
	 * 2017-02-16
	 * @override
	 * @see https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/view/payment/default.js#L127-L159
	 * @used-by https://github.com/magento/magento2/blob/2.1.0/lib/web/knockoutjs/knockout.js#L3863
	 * @param {this} _this
	*/
	placeOrder: function(_this) {
		if (this.validate()) {
			if (!this.isNewCardChosen()) {
				/**
				 * 2016-08-23
				 * Комментарий для Stripe:
				 * Идентификаторы карт начинаются с приставки «card_»
				 * (например: «card_18lGFRFzKb8aMux1Bmcjsa5L»),
				 * а идентификаторы токенов — с приставки «tok_»
				 * (например: «tok_18lWSWFzKb8aMux1viSqpL5X»),
				 * тем самым по приставке мы можем отличить карты от токенов,
				 * и поэтому для карт и токенов мы можем использовать одну и ту же переменную.
				 */
				this.token = this.currentCard();
				this.placeOrderInternal();
			}
			else {
				this.tokenCreate(this.tokenParams(), function(status, resp) {
					if (!_this.tokenCheckStatus(status)) {
						_this.showErrorMessage(_this.tokenErrorMessage(status, resp));
					}
					else {
						_this.token = _this.tokenFromResponse(resp);
						_this.placeOrderInternal();								
					}
				});
			}
		}
	},	
});});