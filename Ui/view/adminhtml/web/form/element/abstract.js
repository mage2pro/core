// 2021-06-13
// 1) "Magento 2.3.5 does not warn about an incorrect (e.g. negative) value of the «Qty» field of the «Advanced Inventory» popup
// on the backend product form": https://github.com/canadasatellite-ca/site/issues/180
// 2) "How is the backend configurable product's form validated on the «Save» button click?": https://mage2.pro/t/6259
define(['jquery', 'uiRegistry'], function($, reg) {
	var inSaveFormHandler = false;
	var warned;
	$(window).on('df.configurable.variations.saveFormHandler.begin', function() {
		inSaveFormHandler = true;
		warned = {};
	});
	$(window).on('df.configurable.variations.saveFormHandler.end', function() {
		inSaveFormHandler = false;
	});
	'use strict'; return function(sb) {return sb.extend({
		/**
		 * 2021-06-13
		 * @override
		 * @see https://github.com/magento/magento2/blob/2.4.2-p1/app/code/Magento/Ui/view/base/web/js/form/element/abstract.js#L397-L423
		 * @returns {*}
		 */
		validate: function() {
			var r = this._super();
			var nameA;
			var modalI;
			// 2021-06-13
			// 1) https://caniuse.com/array-find
			// https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/find#browser_compatibility
			// 2) https://caniuse.com/arrow-functions
			if (!r.valid
				&& inSaveFormHandler
				&& !warned[this.name]
				&& -1 < (modalI = (nameA = this.name.split('.')).findIndex(v => -1 < v.indexOf('modal')))
			) {
				warned[this.name] = true;
				var modal = nameA[modalI];
				var modalE = reg.get(nameA.slice(0, modalI + 1).join('.'));
				var modalS = !modalE ? modal : `«${modalE.title}» / \`${modal}\``;
				alert(`The currently invisible field «${this.label}» / \`${this.name}\` (it is shown on the popup window ${modalS}) has an invalid value «${this.value()}».`);
			}
			return r;
		}
	});};
});