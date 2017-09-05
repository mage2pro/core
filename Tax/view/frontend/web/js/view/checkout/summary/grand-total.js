// 2016-08-18
define([
	'df'
	,'Magento_Checkout/js/model/quote'
	,'Magento_Tax/js/view/checkout/summary/grand-total'
], function(df, quote, parent) {'use strict';
/** 2017-09-06 @uses Class::extend() https://github.com/magento/magento2/blob/2.2.0-rc2.3/app/code/Magento/Ui/view/base/web/js/lib/core/class.js#L106-L140 */	
return parent.extend({
	/**
	 * 2016-08-18
	 * @override
	 * @see https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Tax/view/frontend/web/js/view/checkout/summary/grand-total.js#L47-L53
	 * @used-by https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Tax/view/frontend/web/template/checkout/summary/grand-total.html#L35-L42
	 *
	 * Если валюта заказа отличается от учётной валюты системы,
	 * то Magento показывает сообщение «Your credit card will be charged for...»
	 * с суммой заказа в учётной валюте системы: https://mage2.pro/t/1959
	 *
	 * При этом мои платёжные модули отныне выполняют платёжные транзакции в валюте заказа,
	 * а не в учётной валюте системы: https://mage2.pro/t/1843/4
	 * Поэтому конкретно для моих модулей сообщение «Your credit card will be charged for...»
	 * не только не нужно, но и ошибочно.
	 * @returns {Boolean}
	 */
	isBaseGrandTotalDisplayNeeded: function() {
		/** @type {Boolean} */
		var result;
		/** @type {?Object} */
		var m = quote.getPaymentMethod()();
		if (m) {
			/** @type {?String} */
			var code = m['method'];
			if (code && 0 === code.indexOf('dfe_')) {
				result = false;
			}
		}
		return df.d(result) ? result : this._super();
	}
});});
