// 2020-07-08 "Add an unique CSS class to each payment method container": https://github.com/mage2pro/core/issues/102
define(['jquery', 'mage/utils/wrapper'], function($, w) {'use strict'; return function(sb) {
$.extend(sb.prototype, {
	/**
	 * 2020-07-08
	 * «Called when another element was added to current component»
	 * @see uiComponent::initElement():
	 *		initElement: function(elem) {
	 *			elem.initContainer(this);
	 *			return this;
	 *		},
	 * https://github.com/magento/magento2/blob/2.3.5-p1/app/code/Magento/Ui/view/base/web/js/lib/core/collection.js#L50-L60
	 */
	initElement: w.wrap(sb.prototype.initElement, function(_super, child) {
		if (!this._initElement) {
			this._initElement = true;
			$(document.getElementById(this.index))
				.parent('.payment-method-title')
				.parent('.payment-method')
				.addClass('df-' + this.index)
			;
		}
		return _super();
	})
});
return sb;};});