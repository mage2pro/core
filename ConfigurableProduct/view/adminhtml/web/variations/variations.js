// 2021-06-14
// 1) "Magento 2.3.5 does not warn about an incorrect (e.g. negative) value of the «Qty» field of the «Advanced Inventory» popup
// on the backend product form": https://github.com/canadasatellite-ca/site/issues/180
// 2) "How is the backend configurable product's form validated on the «Save» button click?": https://mage2.pro/t/6259
define(['jquery'], function($) {'use strict'; return function(sb) {return sb.extend({
	/**
	 * 2021-06-14
	 * @override
	 * @see https://github.com/magento/magento2/blob/2.4.2-p1/app/code/Magento/ConfigurableProduct/view/adminhtml/web/js/variations/variations.js#L382-L405
	 */
	saveFormHandler: function() {
		$(window).trigger('df.configurable.variations.saveFormHandler.begin');
		this._super();
		$(window).trigger('df.configurable.variations.saveFormHandler.end');
	}
});};});