/**
 * 2016-06-28
 * Файл https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/model/place-order.js
 * отсутствует в версиях ранее 2.1.0, поэтому эмулируем его:
 * https://github.com/CKOTech/checkout-magento2-plugin/issues/3
 * 2017-04-04
 * Отныне эта библиотека стала универсальной и используется не только для размещения заказа,
 * но и для получения некоторыми платёжными модулями (Klarna) дополнительной информации.
 * @used-by 
 */
define([
	'mage/storage', 'Magento_Checkout/js/model/error-processor'
   ,'Magento_Checkout/js/model/full-screen-loader'
], function (storage, errorProcessor, busy) {'use strict'; return function (url, data, msgContainer) {
	busy.startLoader();
	return storage.post(url, JSON.stringify(data))
		.fail(function(resp) {
			errorProcessor.process(resp, msgContainer);
			busy.stopLoader();
		})
	;
};});