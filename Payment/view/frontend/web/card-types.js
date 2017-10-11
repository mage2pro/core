/**
 * 2017-04-28
 * 2017-07-28
 * This script redefined the standard script
 * 'Magento_Payment/js/model/credit-card-validation/credit-card-number-validator/credit-card-type'
 * via requirejs-config:
 *		var config = {paths: {
 *			'Magento_Payment/js/model/credit-card-validation/credit-card-number-validator/credit-card-type':
 *				'Df_Payment/card-types'
 *	}};
 * https://github.com/mage2pro/core/blob/2.9.14/Payment/view/frontend/requirejs-config.js#L5
 */
define(['df', 'df-lodash'], function (df, _) {'use strict';
var types = [
	{
		title: 'American Express',
		type: 'AE',
		pattern: /^3([47]\d*)?$/,
		isAmex: true,
		gaps: [4, 10],
		lengths: [15],
		code: {name: 'CID', size: 4}
	}
	,{
		title: 'Diners',
		type: 'DN',
		pattern: /^(3(0[0-5]|095|6|[8-9]))\d*$/,
		gaps: [4, 10],
		lengths: [14, 16, 17, 18, 19],
		code: {name: 'CVV', size: 3}
	}
	,{
		title: 'Discover',
		type: 'DI',
		// 2017-10-11
		// "The «6011111111111117» bank card is not detected as a Discover's one":
		// https://github.com/mage2pro/core/issues/35
		// The new regular exression is from here:
		// https://stackoverflow.com/a/13500918
		// http://rubular.com/r/FXWY8ND0C9
		pattern: /^6(?:011\d{12}|5\d{14}|4[4-9]\d{13}|22(?:1(?:2[6-9]|[3-9]\d)|[2-8]\d{2}|9(?:[01]\d|2[0-5]))\d{10})$/,
		gaps: [4, 8, 12],
		lengths: [16, 17, 18, 19],
		code: {name: 'CID', size: 3}
	}
	,{
		code: {name: 'CVC', size: 3}
		,exact: true
		,gaps: [4, 8, 12]
		,lengths: [16]
		// 2017-04-28 «How to validate an Elo bank card number?» https://mage2.pro/t/3867
		// A validator from Moip: https://github.com/moip/credit-card-validator/blob/4786855a/src/main/java/br/com/moip/creditcard/EloCreditCard.java#L8-L53
		,pattern: {test: function(n) {var bin, binI; return 6 === (bin = n.substring(0, 6)).length && (
			_.includes([401178, 401179, 431274, 438935, 451416, 457393, 457631, 457632, 504175, 627780,
						636297, 636368, 636369], binI = df.int(bin))
			|| _.find([[506699, 506778],[509000, 509999],[650031, 650033],[650035, 650051],[650405, 650439],[650485, 650538],[650541, 650598],[650700, 650718],[650720, 650727],[650901, 650920],[651652, 651679],[655000, 655019],[655021, 655058]], function(r) {return binI >= r[0] && binI <= r[1];})
		);}}
		,title: 'Elo'
		,type: 'Elo'
	}
	,{
		code: {name: 'CVC', size: 3}
		,exact: true
		,gaps: [4, 8, 12]
		,lengths: [16]
		// 2017-04-28
		// https://www.itau.com.br/cartoes/escolha/itaucard-20-nacional-hiper.html
		// «How to validate an «Itaucard 2.0 Nacional Hiper» bank card number?» https://mage2.pro/t/3865
		// A validator from Moip:
		// 	private static Set<String> HIPER_BINS = new TreeSet<String>( Arrays.asList(
		//		"637095", "637612", "637599", "637609", "637568"
		//	));
		//	public static boolean isBrandHiper(final String number) {
		//		return number != null
		//		&& number.length() == 16
		//		&& HIPER_BINS.contains(number.substring(0,6));
		//	}
		// https://github.com/moip/credit-card-validator/blob/4786855a/src/main/java/br/com/moip/creditcard/HiperCreditCard.java#L9-L17
		,pattern: /^(637095|637612|637599|637609|637568)\d{10}$/
		,title: 'Itaucard 2.0 Nacional Hiper'
		,type: 'Hiper'
	}
	,{
		code: {name: 'CVC', size: 3}
		,exact: true
		,gaps: [4, 8, 12]
		,lengths: [16, 19]
		// 2017-04-28 «How to validate a Hipercard number?» https://mage2.pro/t/3865
		// 1) A validator from Moip:
		// number != null && (
		// 		number.matches("^606282[0-9]{10}$") || number.matches("^3841(0|4|6)0[0-9]{13}$")
		// )
		// https://github.com/moip/credit-card-validator/blob/4786855a/src/main/java/br/com/moip/creditcard/HipercardCreditCard.java#L6-L7
		// 2) https://gist.github.com/fhferreira/3adc422e40bc31a39679
		// 3) https://gist.github.com/danielfilho/9cd8fb61a39778be587c#bin-e-padrões-para-validação-de-cartão-de-crédito
		,pattern: /^(606282\d{10}(\d{3})?)|(3841(0|4|6)0\d{13})$/
		,title: 'Hipercard'
		,type: 'Hipercard'
	}
	,{
		title: 'JCB',
		type: 'JCB',
		pattern: /^35(2[8-9]|[3-8])\d*$/,
		gaps: [4, 8, 12],
		lengths: [16, 17, 18, 19],
		code: {name: 'CVV', size: 3}
	}
	,{
		title: 'Maestro Domestic',
		type: 'MD',
		pattern: /^6759(?!24|38|40|6[3-9]|70|76)|676770|676774\d*$/,
		gaps: [4, 8, 12],
		lengths: [12, 13, 14, 15, 16, 17, 18, 19],
		code: {name: 'CVC', size: 3}
	}
	,{
		title: 'Maestro International',
		type: 'MI',
		pattern: /^(5(0|[6-9])|63|67(?!59|6770|6774))\d*$/,
		gaps: [4, 8, 12],
		lengths: [12, 13, 14, 15, 16, 17, 18, 19],
		code: {name: 'CVC', size: 3}
	}
	,{
		title: 'MasterCard',
		type: 'MC',
		pattern: /^(?:5[1-5][0-9]{2}|222[1-9]|22[3-9][0-9]|2[3-6][0-9]{2}|27[01][0-9]|2720)[0-9]{12}$/,
		gaps: [4, 8, 12],
		lengths: [16],
		code: {
			name: 'CVC', size: 3}
	}
	,{
		title: 'UnionPay',
		type: 'UN',
		pattern: /^(622(1(2[6-9]|[3-9])|[3-8]|9([[0-1]|2[0-5]))|62[4-6]|628([2-8]))\d*?$/,
		gaps: [4, 8, 12],
		lengths: [16, 17, 18, 19],
		code: {name: 'CVN', size: 3}
	}
	,{
		title: 'Visa',
		type: 'VI',
		pattern: /^4\d*$/,
		gaps: [4, 8, 12],
		lengths: [16],
		code: {name: 'CVV', size: 3}
	}
];
return {
	/**
	 * 2017-04-28
	 * @used-by Magento_Payment/js/model/credit-card-validation/credit-card-number-validator
	 * 		potentialTypes = creditCardTypes.getCardTypes(value);
	 * https://github.com/magento/magento2/blob/2.1.6/app/code/Magento/Payment/view/base/web/js/model/credit-card-validation/credit-card-number-validator.js#L48
	 * @param {?String} n
	 * @return {Object[]}
	 */
	getCardTypes: function(n) {
		/** @type {Object[]} */ var result;
		/** @type {?Object} */ var exact;
		return (exact = _.find(result = _.isUndefined(n) || null === n ? [] : df.a.clone(
			'' === n ? types : _.filter(types, function(v) {return v.pattern.test(n);})
		), function(v) {return v.exact;})) ? [exact] : result
	;}
};});