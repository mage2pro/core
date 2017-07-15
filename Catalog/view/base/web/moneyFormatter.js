/**
 * 2017-07-15
 * Note 1.
 * An improvement for Magento_Catalog/js/price-utils::formatPrice():
 * https://github.com/magento/magento2/blob/2.2.0-RC1.4/app/code/Magento/Catalog/view/base/web/js/price-utils.js#L33-L85
 * Note 2.
 * «The Magento_Catalog/js/price-utils::formatPrice() pollutes the global formatting settings with local ones
 * because of a wrong lodash extend / assignIn usage»: https://mage2.pro/t/4171
 * Note 3.
 * «The global «groupLength» property has an incorrect «,» value instead of a natural number in Magento_Catalog/js/price-utils»: https://mage2.pro/t/4173
 * Note 4. https://mage2.pro/tags/money-format
 */
define([
	'df', 'df-lodash', 'Magento_Catalog/js/price-utils', 'Magento_Checkout/js/model/quote'
], function (df, _, priceUtils, q) {'use strict'; return(
	/**
	 * 2017-07-15
	 * @param {Number} amount
	 * @param {Object=} f
	 * @param {String=} f.decimalSymbol
	 * @param {Number=} f.groupLength
	 * @param {String=} f.groupSymbol
	 * @param {Number=} f.integerRequired
	 * @param {String=} f.pattern
	 * @param {Number=} f.requiredPrecision
	 * @param {Boolean=} isShowSign
	 * @returns {String}
	 */
	function(amount, f, isShowSign) {
		f = _.assign({}, {
			decimalSymbol: ',', groupLength: 3, groupSymbol: ','
			,integerRequired: 1, pattern: '%s', requiredPrecision: 2
		}, q.getPriceFormat(), df.arg(f, {}));
		/** @type {String} */ var decimalSymbol = f.decimalSymbol;
		/** @type {Number} */ var groupLength = f.groupLength;
		/** @type {String} */ var groupSymbol = f.groupSymbol;
		/** @type {Number} */ var integerRequired = f.integerRequired;
		/** @type {Number} */ var precision = f.requiredPrecision;
		/** @type {String} */
		var s = false === isShowSign ? '' : (amount < 0 ? '-' : (true === isShowSign ? '+' : ''));
		/** @type {String} */
		var pattern = -1 === f.pattern.indexOf('{sign}') ? s + f.pattern : f.pattern.replace('{sign}', s);
		// 2017-07-15
		// «We're avoiding the usage of to fixed,
		// and using round instead with the e representation to address numbers like 1.005 = 1.01.
		// Using ToFixed to only provide trailig zeroes in case we have a whole number.»
		// https://github.com/magento/magento2/blob/2.2.0-RC1.4/app/code/Magento/Catalog/view/base/web/js/price-utils.js#L62-L63
		// noinspection JSCheckFunctionSignatures
		amount = Number(Math.round(Math.abs(+amount || 0) + 'e+' + precision) + ('e-' + precision));
		/** @type {String} */ var i = df.int(amount) + '';
		/** @type {Number} */ var pad = i.length >= integerRequired ? 0 : integerRequired - i.length;
		i = priceUtils.strPad('0', pad) + i;
		/** @type {Number} */ var j = i.length <= groupLength ? 0 : i.length % groupLength;
		/** @type {RegExp} */ var re = new RegExp('(\\d{' + groupLength + '})(?=\\d)', 'g');
		// replace(/-/, 0) is only for fixing Safari bug which appears
		// when Math.abs(0).toFixed() executed on '0' number.
		// Result is '0.-0' :(
		// noinspection JSCheckFunctionSignatures
		/** @type {Number} */
		var am = Number(Math.round(Math.abs(amount - i) + 'e+' + precision) + ('e-' + precision));
		/** @type {String} */ var r =
			(j ? i.substr(0, j) + groupSymbol : '')
			+ i.substr(j).replace(re, '$1' + groupSymbol)
			+ (precision ? decimalSymbol + am.toFixed(2).replace(/-/, 0).slice(2) : '')
		;
		return pattern.replace('%s', r).replace(/^\s\s*/, '').replace(/\s\s*$/, '');
	}
);});