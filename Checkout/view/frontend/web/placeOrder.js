/**
 * 2016-07-01
 * Работает аналогично https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/action/place-order.js
 * но при этом позволяет отсылать запросы по нестандартному адресу route.
 * @used-by Df_Payment/mixin::placeOrderInternal():
 * 	$.when(placeOrderAction(this.getData(), this.messageContainer))
 * https://github.com/mage2pro/core/blob/2.4.23/Payment/view/frontend/web/mixin.js#L293
 * 2017-04-04
 * Нестандартные URL больше не используются:
 * все мои платёжные модули теперь отсылают запросы по адресу «/df-payment/...».
 * Но функцию решил оставить: она и документирована хорошо, и есть потенциал для развития.
 */
define([
	'df', 'Df_Checkout/api', 'Magento_Checkout/js/model/quote'
	,'Magento_Checkout/js/model/url-builder', 'Magento_Customer/js/model/customer'
], function(df, api, q, ub, customer) {'use strict'; return function(main) {
	// 2016-06-09
	// Заметил, что на тестовом сайте ec2-54-229-220-134.eu-west-1.compute.amazonaws.com,
	// где установлена Magento 2.1 RC1, опция saveInAddressBook имеет значение не «null»,
	// как на моём сайте с Magento 2.1 RC2, а «false».
	// Это приводит к сбою при валидации запроса на стороне сервера:
	// «Error occured during "saveInAddressBook" processing. Invalid type for value: "".
	// Expected type: "int".»
	// На своих сайтах никогда такого не замечал.
	// Искусственно меняю «false» на «null».
	/** @type {Object} */
	var ba = q.billingAddress();
	// 2016-07-27
	// Добавляю сегодня в своё ядро
	// функциональность отключения необходимости платёжного адреса,
	// поэтому и здесь надо предусмотреть ситуацию отсутствия платёжного адреса.
	if (ba && false === ba.saveInAddressBook) {
		ba.saveInAddressBook = null;
	}
	/** @type {Boolean} */
	var l = customer.isLoggedIn();
	return api(main,
		ub.createUrl(
			df.s.t('/df-payment/%s/place-order', l?'mine':':quoteId'), l?{}:{quoteId: q.getQuoteId()}
		)
		/**
		 * 2017-04-04
		 * @used-by \Df\Payment\PlaceOrder::guest()
		 * https://github.com/mage2pro/core/blob/2.4.23/Payment/PlaceOrder.php#L21
		 * @used-by \Df\Payment\PlaceOrder::registered()
		 * https://github.com/mage2pro/core/blob/2.4.23/Payment/PlaceOrder.php#L36
		 * @uses Df_Payment/mixin::getData()
		 * https://github.com/mage2pro/core/blob/2.4.23/Payment/view/frontend/web/mixin.js#L222-L228
		 */
		,df.o.merge({cartId: q.getQuoteId(), billingAddress: ba, paymentMethod: main.getData()},
			l?{}:{email: q.guestEmail}
		)
	);
};});
