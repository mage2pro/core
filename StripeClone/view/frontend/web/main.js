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
	 * @returns {Function}
	 */
	tokenCreate: function() {return null;},
	
    /**
	 * 2017-02-16
	 * @abstract
	 * @used-by placeOrder()
	 * @param {Object} status
	 * @returns {String}
	 */
	tokenErrorMessage: function(status) {return null;},		
	
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
	 * @abstract
	 * @used-by placeOrder()
	 * @returns {Boolean}
	 */
	tokenStatusOK: function() {return null;},	
	
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
				this.tokenCreate.call(this.tokenParams(), function(status, resp) {
					if (!_this.tokenStatusOK(status)) {
						_this.showErrorMessage(_this.tokenErrorMessage(status));
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