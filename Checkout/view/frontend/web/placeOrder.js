/**
 * 2016-07-01
 * Работает аналогично https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/action/place-order.js
 * но при этом позволяет отсылать запросы по нестандартному адресу route.
 * @used-by Df_Payment/mixin::placeOrderInternal():
 * 		$.when(placeOrderAction(this.getData(), this.messageContainer))
 * https://github.com/mage2pro/core/blob/2.4.23/Payment/view/frontend/web/mixin.js#L293
 * 2017-04-04
 * Нестандартные URL больше не используются:
 * все мои платёжные модули теперь отсылают запросы по адресу «/df-payment/...».
 * Но функцию решил оставить: она и документирована хорошо, и есть потенциал для развития.
 */
define([
	'df', 'df-lodash', 'Df_Checkout/api', 'jquery', 'Magento_Checkout/js/model/quote'
	,'Magento_Checkout/js/model/url-builder', 'Magento_Customer/js/model/customer'
], function(df, _, api, $, q, ub, customer) {'use strict'; return function(main) {
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
	/** @type {Object} */ var agreements =
		/**
		 * 2018-01-15
		 * 1) "The Mage2.PRO payment modules show the
		 * «Please agree to all the terms and conditions before placing the order» warning
		 * in Magento 2.2 and 2.3 even if the terms and conditions checkbox is checked":
		 * https://github.com/mage2pro/core/issues/65
		 * 2) "[Magento 2.2 / 2.3]
		 * The iPay88 module shows the «Please agree to all the terms and conditions before placing the order»
		 * warning even if the terms and conditions checkbox is checked":
		 * https://github.com/mage2pro/ipay88/issues/7
		 * 3) "What are requirejs-config.js `mixins`?": https://mage2.pro/t/5297
		 * 4) I use the solution from https://github.com/magento/magento2/blob/2.2.2/app/code/Magento/CheckoutAgreements/view/frontend/web/js/model/agreements-assigner.js#L7-L38
		 * I am unable to use this module directly because it was added only at 2016-05-26:
		 * https://github.com/magento/magento2/commit/fd87a6e7
		 * It is absent in Magento < 2.1.0-rc2.
		 * Magento < 2.1.0-rc2 contains a similar code here:
		 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/CheckoutAgreements/view/frontend/web/js/model/place-order-mixin.js#L7-L33
		 */
		!window.checkoutConfig.checkoutAgreements.isEnabled ? {} : {extension_attributes: {agreement_ids:
			/**
			 * 2017-01-15
			 * 1) The first selector is for Magento >= 2.1.0-rc2:
			 * https://github.com/magento/magento2/blob/2.2.2/app/code/Magento/CheckoutAgreements/view/frontend/web/js/model/agreements-assigner.js#L24
			 * The second selector is for Magento < 2.1.0-rc2:
			 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/CheckoutAgreements/view/frontend/web/js/model/place-order-mixin.js#L21
			 * 2) https://api.jquery.com/serializeArray
			 * «The .serializeArray() method creates a JavaScript array of objects,
			 * ready to be encoded as a JSON string.
			 * It operates on a jQuery collection of forms and/or form controls.»
			 * 3) https://lodash.com/docs/4.17.4#map
			 * https://stackoverflow.com/a/41757302
			 */
			_.map($(
				'.payment-method._active div[data-role=checkout-agreements] input'
				+ ',.payment-method._active form[data-role=checkout-agreements]'
			).serializeArray(), 'value')
		}}
	;
	/** @type {Boolean} */ var l = customer.isLoggedIn();
	return api(main,
		// 2017-04-05
		// Для анонимных покупателей q.getQuoteId() — это строка вида «63b25f081bfb8e4594725d8a58b012f7».
		// 2017-04-06
		// Передавать вместо q.getQuoteId() строку типа «guest» по аналогии с «mine» мы не можем,
		// потому что <data><parameter name='cartId' force='true'>%cart_id%</parameter></data>
		// не работает для гостей: The «%cart_id%» value of a «route/data/parameter» branch of an webapi.xml
		// works only for the registered customers, not for the guests: https://mage2.pro/t/3612
		ub.createUrl(df.s.t('/df-payment/%s/place-order', l ? 'mine' : q.getQuoteId()), {})
		/**
		 * 2017-04-04
		 * @used-by \Df\Payment\PlaceOrder::guest()
		 * https://github.com/mage2pro/core/blob/2.4.23/Payment/PlaceOrder.php#L21
		 * @used-by \Df\Payment\PlaceOrder::registered()
		 * https://github.com/mage2pro/core/blob/2.4.23/Payment/PlaceOrder.php#L36
		 * @uses Df_Payment/mixin::getData()
		 * https://github.com/mage2pro/core/blob/2.4.23/Payment/view/frontend/web/mixin.js#L222-L228
		 * 2017-04-06
		 * Замечание №1.
		 * Для зарегистрированных покупателей «cartId» передавать нет смысла
		 * (хотя ядро в своей ситуации передаёт), потому что это значение всё равно перетрётся
		 * при применении правила <data><parameter name='cartId' force='true'>%cart_id%</parameter></data>
		 * https://github.com/mage2pro/core/blob/2.4.27/Payment/etc/webapi.xml#L13
		 * How is a «route/data/parameter» branch of an webapi.xml interpreted? https://mage2.pro/t/3603
		 * Замечание №2.
		 * А для гостей тоже нет смысла передавать, потому что это значение уже передаётся в URL:
		 * <route url='/V1/df-payment/:cartId/place-order' method='POST'>
		 * https://github.com/mage2pro/core/blob/2.4.27/Payment/etc/webapi.xml#L6
		 */
		,_.assign({ba: ba, qp: df.o.merge(main.getData(), agreements)}, l ? {} : {email: q.guestEmail})
	);
};});
