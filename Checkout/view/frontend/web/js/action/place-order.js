/**
 * 2016-07-01
 * Работает аналогично https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/action/place-order.js
 * но при этом позволяет отсылать запросы по нестандартному адресу route.
 * @used-by Df_Payment/mixin::placeOrderInternal():
 * 	$.when(placeOrderAction(this.getData(), this.messageContainer))
 * https://github.com/mage2pro/core/blob/2.4.23/Payment/view/frontend/web/mixin.js#L293
 * 2017-04-04
 * @param {Object} paymentData
 * Параметры, которые платёжная форма передаёт на сервер.
 *	getData: function () {return {
 *		// 2016-05-03
 *		// Если не засунуть данные (например, «token») внутрь «additional_data», то получим сбой типа:
 *		// «Property "Token" does not have corresponding setter
 *		// in class "Magento\Quote\Api\Data\PaymentInterface».
 *		additional_data: this.dfData(), method: this.item.method
 *	};},
 * https://github.com/mage2pro/core/blob/2.4.23/Payment/view/frontend/web/mixin.js#L222-L228
 */
define([
	'Df_Checkout/js/model/place-order'
	,'Magento_Checkout/js/model/quote'
	,'Magento_Checkout/js/model/url-builder'
	,'Magento_Customer/js/model/customer'
], function (placeOrderService, quote, urlBuilder, customer) {
	'use strict';
	return function (paymentData, messageContainer) {
		// 2016-06-09
		// Заметил, что на тестовом сайте ec2-54-229-220-134.eu-west-1.compute.amazonaws.com,
		// где установлена Magento 2.1 RC1, опция saveInAddressBook имеет значение не «null»,
		// как на моём сайте с Magento 2.1 RC2, а «false».
		// Это приводит к сбою при валидации запроса на стороне сервера:
		// «Error occured during "saveInAddressBook" processing. Invalid type for value: "".
		// Expected type: "int".»
		// На своих сайтах никогда такого не замечал.
		// Искусственно меняю «false» на «null».
		var address = quote.billingAddress();
		// 2016-07-27
		// Добавляю сегодня в своё ядро
		// функциональность отключения необходимости платёжного адреса,
		// поэтому и здесь надо предусмотреть ситуацию отсутствия платёжного адреса.
		if (address && false === address.saveInAddressBook) {
			address.saveInAddressBook = null;
		}
		var payload = {cartId: quote.getQuoteId(), billingAddress: address, paymentMethod: paymentData};
		var serviceUrl;
		if (customer.isLoggedIn ()) {
			serviceUrl = urlBuilder.createUrl('/df-payment/mine/place-order', {});
		}
		else {
			serviceUrl = urlBuilder.createUrl('/df-payment/:quoteId/place-order', {
				quoteId: quote.getQuoteId ()
			});
			payload.email = quote.guestEmail;
		}
		return placeOrderService(serviceUrl, payload, messageContainer);
	};
});
