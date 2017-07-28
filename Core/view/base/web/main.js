/**
 * 2016-11-30
 * «How to get the current design package and theme names programmatically
 * on the browser side (with JavaScript)?» https://mage2.pro/t/2350
 * 2017-04-13
 * Today I have noticed with the current Magento 2.2-dev version,
 * that require could be an object without any methods here.
 * «require.toUrl is not a function»: https://mage2.pro/t/3532
 * 2017-07-27
 * Today I have added the `defer` attribute to the script declaration:
 * 		<script src='Df_Core::main.js' defer='defer'/>
 * Maybe now we do not need to check for require.toUrl.
 */
if (require.toUrl) {
	/** @type {String} */
	var a = require.toUrl('').split('/').slice(-4);
	// 2016-11-30
	// The DOM Ready event is intentionally ignored, because we need only the root <html> tag.
	// document.documentElement is the root <html> tag: http://stackoverflow.com/a/22873490
	// A possible result class: « df-package-Magento df-theme-luma»
	document.documentElement.className += ' df-package-' + a[0] + ' df-theme-' + a[1];
}
/**
 * 2017-07-27
 * @todo It looks like the standard jQuery form validation does not work for us
 * because of the jQuery Validate plugin code:
 *		.filter(function () {
 *			if (!this.name && validator.settings.debug && window.console) {
 *				console.error("%o has no name assigned", this);
 *			}
 *			// select only the first element for each name, and only those with rules specified
 *			if (this.name in rulesCache || !validator.objectLength($(this).rules())) {
 *				return false;
 *			}
 *			rulesCache[this.name] = true;
 *			return true;
 *		});
 * https://github.com/magento/magento2/blob/2.2.0-RC1.5/lib/web/jquery/jquery.validate.js#L487-L499
 * Stripe and its clones forbids us to assign the `name` attribute to the bank card form elements,
 * and the jQuery Validate plugin acccounts the elements by their names,
 * so it does not work for our bank card forms.
 */
require(['jquery', 'jquery/validate'], function($) {
	$.validator.prototype.elements = function() {
		var _this = this;
		var rulesCache = {};
		// select all valid inputs inside the form (no submit or reset buttons)
		return $(this.currentForm)
			.find('input, select, textarea')
			.not(':submit, :reset, :image, [disabled]')
			.not(this.settings.ignore)
			.filter(function() {
				// 2017-07-28
				// It is the essence of my fix: if an input has no name, then I use its ID instead.
				/** @type {String} */ var id = this.name || this.id;
				if (!id && _this.settings.debug && window.console) {
					console.error('%o has no name or ID assigned', this);
				}
				/** @type {Boolean} */ var r;
				// select only the first element for each name, and only those with rules specified
				if (id in rulesCache || !_this.objectLength($(this).rules())) {
					r = false;
				}
				else {
					rulesCache[id] = true;
					r = true;
				}
				return r;
			}
		);
	};
});