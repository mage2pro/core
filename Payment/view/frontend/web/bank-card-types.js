// 2017-04-28
define(['df', 'df-lodash'], function (df, _) {'use strict';
	var types = [
		{
			title: 'Visa',
			type: 'VI',
			pattern: /^4\d*$/,
			gaps: [4, 8, 12],
			lengths: [16],
			code: {name: 'CVV', size: 3}
		},
		{
			title: 'MasterCard',
			type: 'MC',
			pattern: /^(?:5[1-5][0-9]{2}|222[1-9]|22[3-9][0-9]|2[3-6][0-9]{2}|27[01][0-9]|2720)[0-9]{12}$/,
			gaps: [4, 8, 12],
			lengths: [16],
			code: {
				name: 'CVC', size: 3}
		},
		{
			title: 'American Express',
			type: 'AE',
			pattern: /^3([47]\d*)?$/,
			isAmex: true,
			gaps: [4, 10],
			lengths: [15],
			code: {name: 'CID', size: 4}
		},
		{
			title: 'Diners',
			type: 'DN',
			pattern: /^(3(0[0-5]|095|6|[8-9]))\d*$/,
			gaps: [4, 10],
			lengths: [14, 16, 17, 18, 19],
			code: {name: 'CVV', size: 3}
		},
		{
			title: 'Discover',
			type: 'DI',
			pattern: /^(6011(0|[2-4]|74|7[7-9]|8[6-9]|9)|6(4[4-9]|5))\d*$/,
			gaps: [4, 8, 12],
			lengths: [16, 17, 18, 19],
			code: {name: 'CID', size: 3}
		},
		{
			title: 'JCB',
			type: 'JCB',
			pattern: /^35(2[8-9]|[3-8])\d*$/,
			gaps: [4, 8, 12],
			lengths: [16, 17, 18, 19],
			code: {name: 'CVV', size: 3}
		},
		{
			title: 'UnionPay',
			type: 'UN',
			pattern: /^(622(1(2[6-9]|[3-9])|[3-8]|9([[0-1]|2[0-5]))|62[4-6]|628([2-8]))\d*?$/,
			gaps: [4, 8, 12],
			lengths: [16, 17, 18, 19],
			code: {name: 'CVN', size: 3}
		},
		{
			title: 'Maestro International',
			type: 'MI',
			pattern: /^(5(0|[6-9])|63|67(?!59|6770|6774))\d*$/,
			gaps: [4, 8, 12],
			lengths: [12, 13, 14, 15, 16, 17, 18, 19],
			code: {name: 'CVC', size: 3}
		},
		{
			title: 'Maestro Domestic',
			type: 'MD',
			pattern: /^6759(?!24|38|40|6[3-9]|70|76)|676770|676774\d*$/,
			gaps: [4, 8, 12],
			lengths: [12, 13, 14, 15, 16, 17, 18, 19],
			code: {name: 'CVC', size: 3}
		}
		// 2017-04-28
		// «How to validate a Hipercard number?» https://mage2.pro/t/3865
		// https://gist.github.com/fhferreira/3adc422e40bc31a39679
		// https://gist.github.com/danielfilho/9cd8fb61a39778be587c#bin-e-padrões-para-validação-de-cartão-de-crédito
		,{
			title: 'Hipercard',
			type: 'Hipercard',
			pattern: /^(606282\d{10}(\d{3})?)|(3841\d{15})$/,
			gaps: [4, 8, 12],
			lengths: [13, 16, 19],
			code: {name: 'CVC', size: 3}
		}
	];
	return {
		/**
		 * 2017-04-28
		 * @param {*} n
		 * @return {Object[]}
		 */
		getCardTypes: function(n) {return _.isUndefined(n) || null === n ? [] : df.a.clone(
			'' === n ? types : _.filter(types, function(v) {return v.pattern.test(n);})
		);}
	};
});