define([
	'df'
	,'df-lodash'
	,'Df_Catalog/moneyFormatter'
   	,'jquery'
	,'Magento_Catalog/js/price-utils'
	,'Magento_Checkout/js/model/quote'
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
	// 2016-04-17
   	// `How is the «Magento_Customer/js/customer-data» object implemented and used?` https://mage2.pro/t/1246
	,'Magento_Customer/js/customer-data'
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
	,'Magento_Checkout/js/checkout-data'
], function (df, _, mf, $, priceUtils, q, customer, customerData, checkoutData) {'use strict'; return {
	/**
	 * 2016-09-30
	 * @used-by Dfe_Square/main::dfOnRender()
	 * https://github.com/mage2pro/square/blob/2.0.4/view/frontend/web/main.js#L246-L265
	 * @returns {Object=}
	 */
	addressB: function() {return q.billingAddress();},
	/**
	 * 2016-09-30
	 * @used-by Dfe_Square/main::dfOnRender()
	 * https://github.com/mage2pro/square/blob/2.0.4/view/frontend/web/main.js#L246-L265
	 * @returns {Object=}
	 */
	addressS: function() {return q.shippingAddress();},
	/**
	 * 2016-08-25
	 * 3-значный код валюты заказа (не учётной)
	 * @type {String}
	 */
	currency: window.checkoutConfig.quoteData.quote_currency_code,
	/**
	 * 2016-04-20
	 * «How to get the current customer's email on the frontend checkout screen?» https://mage2.pro/t/1295
	 * «How to programmatically check on the frontend checkout screen client side (with JavaScript)
	 * whether the customer is authenticated (logged in)?» https://mage2.pro/t/1303
	 * 2016-06-01
	 * Брать надо именно getValidatedEmailValue(), а не getInputFieldEmailValue():
	 * 1) `What is the difference between «Magento_Checkout/js/checkout-data»'s
	 * getValidatedEmailValue() and getInputFieldEmailValue() methods?` https://mage2.pro/t/1733
	 * 2) `How are the «Magento_Checkout/js/checkout-data»'s
	 * setValidatedEmailValue() and setInputFieldEmailValue() methods
	 * implemeted and used?` https://mage2.pro/t/1734
	 * @used-by Dfe_CheckoutCom/main::placeOrder()
	 * https://github.com/mage2pro/checkout.com/blob/1.4.6/view/frontend/web/main.js#L132-L142
	 * @used-by Dfe_Stripe/main::tokenParams()
	 * @returns {String}
	 */
	email: function() {return (
		window.isCustomerLoggedIn ? window.customerData.email : checkoutData.getValidatedEmailValue()
	);},
	/**
	 * 2016-08-07
	 * How to format a money value (e.g. price) in JavaScript?  https://mage2.pro/t/1932
	 * @param {Number} amount
	 * @param {Object=} format
	 * @returns {String}
	 */
	formatMoney: function(amount, format) {return mf(amount, format);},
	/**
	 * 2017-07-15
	 * @param {Number} amount
	 * @param {Object=} format
	 * @returns {String}
	 */
	formatMoneyH: function(amount, format) {return mf(amount, _.assign({
		patternDecimals: "<span class='df-decimals'>%s</span>"
		,patternGlobal: "<span class='df-money'>%s</span>"
		,patternInteger: "<span class='df-integer'>%s</span>"
	}, df.arg(format, {})));},
	/**
	 * 2016-09-30
	 * @returns {jqXHR}
	 */
	geo: df.c(function() {return $.getJSON('//freegeoip.net/json/');}),
	/**
	 * 2016-07-16
	 * Returns the current quote's grand total value.
	 * By analogy with https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/view/summary/grand-total.js#L20-L26
	 * How to get the current quote's grant total value
	 * on the frontend checkout page's client side?  https://mage2.pro/t/1873
	 *
	 * 2016-08-07
	 * Note 1.
	 * Previously, I have used the following code here:
	 *
	 * var totals = q.getTotals()()
	 * return totals['grand_total']
	 *
	 * But today I have noticed that it can ignore the taxes
	 * (may be not always, but with a particular backend settings combination).
	 *
	 * Note 2.
	 * Another solution is to use the 'Magento_Checkout/js/model/totals' class instance
	 * as follows: totals.getSegment('grand_total').value
	 * However, the current implementation of getSegment() non-optimal:
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/model/totals.js#L32-L50
	 *
	 * 2016-09-06
	 * I have noticed today, that while totals['grand_total'] does not include the taxes,
	 * the totals['base_grand_total'] includes the taxes, for example:
	 * base_grand_total: 83.83
	 * grand_total: 74.7
	 * base_tax_amount: 9.13
	 * tax_amount: 9.13
	 * It allows me to use a shorter implementation for the grandTotalBase() method below.
	 *
	 * 2017-03-03
	 * The previous implementation was:
	 *
	 * var totals = q.getTotals()();
	 * var segments = totals['total_segments'];
	 * return segments[segments.length - 1].value;
	 *
	 * But today I have noticed an incorrect behavior of the aheadWorks Gift Card extension:
	 * «aheadWorks Gift Card adds its entry at the end of a totals array,
	 * and its «value» is incorrect (always 0)»: https://mage2.pro/t/3499
	 *
	 * https://lodash.com/docs/4.17.4#find
	 * https://lodash.com/docs/4.17.4#findLast
	 *
	 * @returns {Number}
	 */
	grandTotal: function() {return parseFloat(
		_.findLast(q.getTotals()()['total_segments'], 'value').value
	);},
	/**
	 * 2016-09-06
	 * @returns {Number}
	 */
	grandTotalBase: function() {return q.getTotals()()['base_grand_total'];}
};});