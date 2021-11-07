// 2017-07-12
define(['df', 'jquery', 'mage/validation'], function(df, $) {return {
	/**
	 * 2017-07-09
	 * «How to implement a custom JavaScript validator for a backend configuration field?»: https://mage2.pro/t/4154
	 * @used-by https://github.com/mage2pro/core/blob/5.3.2/Customer/view/base/web/validator/cpf.js#L34
	 * @used-by https://github.com/mage2pro/core/blob/5.3.2/Ui/view/base/web/validator/popular.js#L7
	 * @used-by https://github.com/mage2pro/phone/blob/1.0.13/view/base/web/validator.js#L11
	 * @param {String} id
	 * @param {Function} f
	 * @param {String} s
	 */
	add: function(id, f, s) {$.validator.addMethod('df-validate-' + id, function(v, e) {return this.optional(e) || f(v);}, s);}
	/**
	 * 2017-07-12
	 * @used-by https://github.com/mage2pro/core/blob/5.3.2/Customer/view/base/web/validator/cpf.js#L34
	 * @used-by https://github.com/mage2pro/core/blob/5.3.2/Ui/view/base/web/validator/popular.js#L7
	 * @used-by https://github.com/mage2pro/phone/blob/1.0.13/view/base/web/validator.js#L11
	 * @param {String} s
	 * @returns {String}
	 */
	,peav: function(s) {return df.t($.mage.__('Please enter a valid %s.'), $.mage.__(s));}
};});