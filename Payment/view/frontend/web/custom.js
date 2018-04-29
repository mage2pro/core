/**
 * 2016-07-18
 * @see Df_Payment/withOptions https://github.com/mage2pro/core/blob/2.9.2/Payment/view/frontend/web/withOptions.js
 * @see Dfe_Dragonpay/main https://github.com/mage2pro/dragonpay/blob/1.0.9/view/frontend/web/main.js
 * @see Dfe_Klarna/main https://github.com/mage2pro/klarna/blob/0.3.8/view/frontend/web/main.js
 * @see Dfe_Moip/boleto https://github.com/mage2pro/moip/blob/1.0.15/view/frontend/web/boleto.js
 * @see Dfe_PostFinance/main https://github.com/mage2pro/postfinance/blob/1.0.1/view/frontend/web/main.js
 * @see Dfe_Qiwi/main https://github.com/mage2pro/qiwi/blob/0.1.6/view/frontend/web/main.js
 */
define([
	'./mixin', 'df', 'Magento_Checkout/js/view/payment/default'
], function(mixin, df, parent) {'use strict';
/** 2017-09-06 @uses Class::extend() https://github.com/magento/magento2/blob/2.2.0-rc2.3/app/code/Magento/Ui/view/base/web/js/lib/core/class.js#L106-L140 */
return parent.extend(df.o.merge(mixin, {
	/**
	 * 2016-08-24
	 * @override
	 * @see Magento_Checkout/js/view/payment/default::initialize()
	 * https://github.com/magento/magento2/blob/2.1.5/app/code/Magento/Checkout/view/frontend/web/js/view/payment/default.js#L53-L91
	 * @see Df_Payment/withOptions::initialize()
	 * @see Dfe_Moip/boleto::initialize()
	 * @returns {exports}
	*/
	initialize: function() {
		this._super();
		mixin.initialize.apply(this);
		return this;
	},
	/**
	 * 2016-07-01
	 * @override
	 * @see Magento_Checkout/js/view/payment/default::placeOrder()
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.5/app/code/Magento/Checkout/view/frontend/web/js/view/payment/default.js#L126-L158
	 *		placeOrder: function(data, event) {
	 *			var self = this;
	 *			if (event) {
	 *				event.preventDefault();
	 *			}
	 *			if (this.validate() && additionalValidators.validate()) {
	 *				this.isPlaceOrderActionAllowed(false);
	 *				this.getPlaceOrderDeferredObject()
	 *				.fail(function() {self.isPlaceOrderActionAllowed(true);})
	 *				.done(function() {
	 *					self.afterPlaceOrder();
	 *					if (self.redirectAfterPlaceOrder) {
	 *						redirectOnSuccessAction.execute();
	 *					}
	 *				});
	 *				return true;
	 *			}
	 *			return false;
	 *		},
	 * @used-by Df_Payment/main.html:
	 *		<button
	 *			class="action primary checkout"
	 *			type="submit"
	 *			data-bind="
	 *				click: placeOrder
	 *				,css: {disabled: !isPlaceOrderActionAllowed()}
	 *				,enable: dfIsChosen()
	 *			"
	 *			disabled
	 *		>
	 *			<span data-bind="df_i18n: 'Place Order'"></span>
	 *		</button>
	 * https://github.com/mage2pro/core/blob/2.9.10/Payment/view/frontend/web/template/main.html#L57-L68
	 * https://github.com/magento/magento2/blob/2.1.0/lib/web/knockoutjs/knockout.js#L3863
	 * @param {this} _this
	 * @param {Event} event
	 */
	placeOrder: function(_this, event) {
		if (event) {
			event.preventDefault();
		}
		if (this.validate()) {
			// 2017-07-26 «Sometimes getting duplicate orders in checkout»: https://mage2.pro/t/4217
			this.state_waitingForServerResponse(true);
			this.placeOrderInternal();
		}
	},
	/**
	 * 2017-09-09
	 * @override
	 * @see Magento_Checkout/js/view/payment/default
	 * @used-by placeOrder()
	 * @returns {Boolean}
	*/
	validate: function() {return this._super() && mixin.validate.apply(this);}
}));});


