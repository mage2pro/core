// 2017-07-12
define(['df', 'jquery', 'mage/validation'], function(df, $) {return {
	/**
	 * 2017-07-09
	 * «How to implement a custom JavaScript validator for a backend configuration field?»
	 * https://mage2.pro/t/4154
	 * 2017-07-12
	 * @param {String} id
	 * @param {Function} f
	 * @param {String} s
	 */
	add: function(id, f, s) {$.validator.addMethod(
		'df-validate-' + id, function(v, e) {return this.optional(e) || f(v);}, s
	);}
	/**
	 * 2017-07-12
	 * @param {String} s
	 * @returns {String}
	 */
	,peav: function(s) {return df.t($.mage.__('Please enter a valid %s.'), $.mage.__(s));}
};});