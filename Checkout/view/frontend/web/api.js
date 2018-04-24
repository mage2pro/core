/**
 * 2016-06-28
 * Файл https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/model/place-order.js
 * отсутствует в версиях ранее 2.1.0, поэтому эмулируем его:
 * https://github.com/CKOTech/checkout-magento2-plugin/issues/3
 * 2017-04-04
 * Отныне эта библиотека стала универсальной и используется не только для размещения заказа,
 * но и для получения некоторыми платёжными модулями (Klarna) дополнительной информации.
 * @used-by Df_Checkout/placeOrder
 * https://github.com/mage2pro/core/blob/2.4.26/Checkout/view/frontend/web/placeOrder.js#L50-L65
 * @used-by Dfe_Klarna/main
 */
define([
	'df'
	,'Df_Core/thirdParty/URI/URI'
   	,'mage/storage'
   	,'Magento_Checkout/js/model/error-processor'
	,'Magento_Checkout/js/model/full-screen-loader'
	,'Magento_Ui/js/model/messageList'
], function (df, lURI, storage, errorProcessor, busy, messageList) {'use strict';
return function(main, url, data, method) {
	busy.startLoader();
	method = method ? method : (df.o.e(data) ? 'get' : 'post');
	if ('get' === method && !df.o.e(data)) {
		url = lURI(url).setQuery(data).toString();
		data = {};
	}
	/**
	 * 2017-04-04
	 * @uses mage/storage::get()
	 * https://github.com/magento/magento2/blob/2.1.5/lib/web/mage/storage.js#L9-L26
	 * @uses mage/storage::post()
	 * https://github.com/magento/magento2/blob/2.1.5/lib/web/mage/storage.js#L27-L46
	 */
	return (df.o.e(data) ? storage[method](url) : storage[method](url, JSON.stringify(data)))
		.always(function() {busy.stopLoader();})
		/**
		 * 2017-04-04
		 * @uses Magento_Checkout/js/view/payment/default::messageContainer
		 * https://github.com/magento/magento2/blob/2.1.5/app/code/Magento/Checkout/view/frontend/web/js/view/payment/default.js#L99
		 * 		this.messageContainer = new Messages();
		 *
		 * 2017-07-30
		 * The previous implementation:
		 * 		errorProcessor.process(resp, main.messageContainer);
		 * https://github.com/mage2pro/core/blob/2.9.20/Checkout/view/frontend/web/api.js#L33
		 * `errorProcessor` is 'Magento_Checkout/js/model/error-processor':
		 *		process: function (response, messageContainer) {
		 *			var error;
		 *			messageContainer = messageContainer || globalMessageList;
		 *			if (response.status == 401) {
		 *				window.location.replace(url.build('customer/account/login/'));
		 *			}
		 *			else {
		 *				error = JSON.parse(response.responseText);
		 *				messageContainer.addErrorMessage(error);
		 *			}
		 *		}
		 * https://github.com/magento/magento2/blob/2.2.0-RC1.6/app/code/Magento/Checkout/view/frontend/web/js/model/error-processor.js#L16-L31
		 * It does not check whether the `response.responseText` as actually a JSON.
		 * For example, if we have an uncatched failure on the server part,
		 * the web server can respond with HTTP code 500,
		 * and the diagnostig message will be a plain text, not JSON, e.g.:
		 *
		 *	<br />
		 *	<b>Fatal error</b>:  Uncaught TypeError: Argument 1 passed to dfa_deep()
		 * 	must be of the type array, null given,
		 *	called in vendor/mage2pro/moip/Facade/Card.php on line 172
		 *	and defined in C:\work\mage2.pro\store\vendor\mage2pro\core\Core\lib\array.php:854
		 *	Stack trace:
		 *	#0 vendor/mage2pro/moip/Facade/Card.php(172): dfa_deep(NULL, 'holder/fullname')
		 *	#1 vendor/mage2pro/core/StripeClone/Block/Info.php(37): Dfe\Moip\Facade\Card-&gt;owner()
		 *	<...>
		 *
		 * So when 'Magento_Checkout/js/model/error-processor' tries to parse such message as a JSON,
		 * it fails with another error (JSON parsing error),
		 * and the original message is not shown to the customer at all.
		 *
		 * So today I have implemented my own failure handling.
		 */
		.fail(function(resp) {
			try {errorProcessor.process(resp, main.messageContainer || messageList);}
			catch(ignore) {
				/**
				 * 2017-07-30
				 * @see Magento_Ui/js/model/messages::add():
				 * 		type.push(messageObj.message);
				 * https://github.com/magento/magento2/blob/2.2.0-RC1.6/app/code/Magento/Ui/view/frontend/web/js/model/messages.js#L44
				 */
				(main.messageContainer || messageList).addErrorMessage({
					message: df.isLocalhost() ? resp.responseText : 'An unexpected error occured'
				});
			}
		})
	;
};});