define([
	'Magento_Checkout/js/model/quote'
	/**
	 * 2016-04-17
	 * How to get the customer's data on the frontend checkout page's client side (with JavaScript)
	 * using the «Magento_Customer/js/model/customer» object?
	 * https://mage2.pro/t/1245
	 *
	 * The «Magento_Customer/js/model/customer» JavaScript object interface
	 * https://mage2.pro/t/1252
	 */
	,'Magento_Customer/js/model/customer'
	/**
	 * 2016-04-17
	 * How is the «Magento_Customer/js/customer-data» object implemented and used?
	 * https://mage2.pro/t/1246
	 */
	, 'Magento_Customer/js/customer-data'
	/**
	 * 2016-04-17
	 * How is the «Magento_Checkout/js/checkout-data» object implemented and used?
	 * https://mage2.pro/t/1293
	 *
	 * How to get the checkout data on the frontend checkout page's client side (with JavaScript)?
	 * https://mage2.pro/t/1292
	 *
	 * https://mage2.pro/t/1294
	 * The «Magento_Checkout/js/checkout-data» JavaScript object interface and its implementation
	 */
	, 'Magento_Checkout/js/checkout-data'
], function (quote, customer, customerData, checkoutData) {
    'use strict';
	return {
		/**
		 * 2016-04-20
		 * How to get the current customer's email on the frontend checkout screen?
		 * https://mage2.pro/t/1295
		 * @returns {String}
		 */
		email: function() {
			/**
			 * 2016-04-20
			 * How to programmatically check on the frontend checkout screen client side (with JavaScript) whether the customer is authenticated (logged in)?
			 * https://mage2.pro/t/1303
			 */
 			return (
				window.isCustomerLoggedIn
				? window.customerData.email
				/**
				 * 2016-06-01
				 * Брать надо именно getValidatedEmailValue(), а не getInputFieldEmailValue():
				 *
				 * What is the difference between «Magento_Checkout/js/checkout-data»'s
				 * getValidatedEmailValue() and getInputFieldEmailValue() methods?
				 * https://mage2.pro/t/1733
				 *
				 * How are the «Magento_Checkout/js/checkout-data»'s
				 * setValidatedEmailValue() and setInputFieldEmailValue() methods
				 * implemeted and used? https://mage2.pro/t/1734
				 */
				: checkoutData.getValidatedEmailValue()
			);
		},
		/**
		 * 2016-07-16
		 * Returns the current quote's grand total value.
		 * By analogy with https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/view/summary/grand-total.js#L20-L26
		 * How to get the current quote's grant total value
		 * on the frontend checkout page's client side?  https://mage2.pro/t/1873
		 * @returns {Number}
		 */
		grandTotal: function() {
			/** @type {Object} */
			var totals = quote.getTotals()();
			return (totals ? totals : quote)['grand_total'];
		}
	}
});