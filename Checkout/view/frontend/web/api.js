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
	'df', 'mage/storage', 'Magento_Checkout/js/model/error-processor'
   ,'Magento_Checkout/js/model/full-screen-loader'
], function (df, storage, errorProcessor, busy) {'use strict'; return function(main, url, data) {
	busy.startLoader();
	/**
	 * 2017-04-04
	 * @uses mage/storage::get()
	 * https://github.com/magento/magento2/blob/2.1.5/lib/web/mage/storage.js#L9-L26
	 * @uses mage/storage::post()
	 * https://github.com/magento/magento2/blob/2.1.5/lib/web/mage/storage.js#L27-L46
	 */
	return (df.o.e(data) ? storage.get(url) : storage.post(url, JSON.stringify(data)))
		.always(function() {busy.stopLoader();})
		/**
		 * 2017-04-04
		 * @uses Magento_Checkout/js/view/payment/default::messageContainer
		 * https://github.com/magento/magento2/blob/2.1.5/app/code/Magento/Checkout/view/frontend/web/js/view/payment/default.js#L99
		 * 		this.messageContainer = new Messages();
		 */
		.fail(function(resp) {errorProcessor.process(resp, main.messageContainer);})
	;
};});