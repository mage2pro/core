/**
 * 2016-07-01
 * Работает аналогично https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/action/place-order.js
 * но при этом позволяет отсылать запросы по нестандартному адресу route.
 */
define ([
	'Magento_Checkout/js/model/quote',
	'Magento_Checkout/js/model/url-builder',
	'Magento_Customer/js/model/customer',
	'Df_Checkout/js/model/place-order'
], function (quote, urlBuilder, customer, placeOrderService) {
	'use strict';
	return function (paymentData, messageContainer, route) {
		var serviceUrl, payload;
		/**
		 * 2016-06-09
		 * Заметил, что на тестовом сайте ec2-54-229-220-134.eu-west-1.compute.amazonaws.com,
		 * где установлена Magento 2.1 RC1, опция saveInAddressBook имеет значение не «null»,
		 * как на моём сайте с Magento 2.1 RC2, а «false».
		 * Это приводит к сбою при валидации запроса на стороне сервера:
		 * «Error occured during "saveInAddressBook" processing. Invalid type for value: "".
		 * Expected type: "int".»
		 * На своих сайтах никогда такого не замечал.
		 * Искусственно меняю «false» на «null».
 		 */
		var address = quote.billingAddress();
		if (address && false === address.saveInAddressBook) {
			address.saveInAddressBook = null;
		}
		payload = {
			cartId: quote.getQuoteId(), billingAddress: address, paymentMethod: paymentData
		};
		if (customer.isLoggedIn ()) {
			serviceUrl = urlBuilder.createUrl('/' + route + '/mine/place-order', {});
		}
		else {
			serviceUrl = urlBuilder.createUrl('/' + route + '/:quoteId/place-order', {
				quoteId: quote.getQuoteId ()
			});
			payload.email = quote.guestEmail;
		}
		return placeOrderService(serviceUrl, payload, messageContainer);
	};
});
