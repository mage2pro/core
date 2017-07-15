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
], function (df, _, priceUtils, q) {'use strict';
	/**
	 * 2017-07-15
	 * «We're avoiding the usage of toFixed(),
	 * and using round instead with the e representation to address numbers like 1.005 = 1.01.
	 * Using toFixed to only provide trailig zeroes in case we have a whole number.»
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.4/app/code/Magento/Catalog/view/base/web/js/price-utils.js#L62-L63
	 * @param {Number} v
	 * @param {Number} prec
	 * @returns {Number}
	 */
	function roundWithPrec(v, prec) {//noinspection JSCheckFunctionSignatures
		return Number(Math.round(Math.abs(+v || 0) + 'e+' + prec) + ('e-' + prec));
	}
return(
	/**
	 * 2017-07-15
	 * @used-by Df_Checkout/data::formatMoney()
	 * @used-by Df_Checkout/data::formatMoneyH()
	 * @param {Number} a
	 * @param {Object=} f
	 * @param {String=} f.decimalSymbol
	 * @param {Number=} f.groupLength
	 * @param {String=} f.groupSymbol
	 * @param {Number=} f.integerRequired
	 * @param {String=} f.pattern
	 * @param {String=} f.patternDecimals
	 * @param {String=} f.patternGlobal
	 * @param {String=} f.patternInteger
	 * @param {Number=} f.requiredPrecision
	 * @param {Boolean=} showSign
	 * @returns {String}
	 */
	function(a, f, showSign) {
		f = _.assign({
			decimalSymbol: ',', groupLength: 3, groupSymbol: ',', patternGlobal: '%s',
			patternInteger: '%s', integerRequired: 1, pattern: '%s', requiredPrecision: 2
		}, q.getPriceFormat(), df.arg(f, {}));
		/** @type {Number} */ var prec = f.requiredPrecision;
		a = roundWithPrec(a, prec);
		/** @type {String} */ var as = df.int(a) + '';
		/** @type {Number} */ var pad = as.length >= f.integerRequired ? 0 : f.integerRequired - as.length;
		as = priceUtils.strPad('0', pad) + as;
		/** @type {Number} */var firstGroupLength = as.length <= f.groupLength ? 0 : as.length % f.groupLength;
		// noinspection JSCheckFunctionSignatures
		return df.s.t(f.patternGlobal,
			_.trim((-1 < f.pattern.indexOf('{sign}') ? f.pattern : '{sign}' + f.pattern)
				.replace('{sign}', false === showSign ? '' : (a < 0 ? '-' : (true === showSign ? '+' : '')))
				.replace('%s',
					df.s.t(f.patternInteger,
						(firstGroupLength ? as.substr(0, firstGroupLength) + f.groupSymbol : '')
						+ as.substr(firstGroupLength).replace(
							new RegExp('(\\d{' + f.groupLength + '})(?=\\d)', 'g'), '$1' + f.groupSymbol
						)
					)
					+ (!prec ? '' :
						// 2017-07-15
						// «replace(/-/, 0) is only for fixing Safari bug
						// which appears when Math.abs(0).toFixed() executed on '0' number.
						// Result is '0.-0' :(»
						// https://github.com/magento/magento2/blob/2.2.0-RC1.4/app/code/Magento/Catalog/view/base/web/js/price-utils.js#L75-L77
						df.s.t(f.patternDecimals || f.decimalSymbol + '%s',
							roundWithPrec(a - as, prec).toFixed(2).replace(/-/, 0).slice(2)
						)
					)
				)
			)
		);
	}
);});