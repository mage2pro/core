// 2016-08-08
// Почему-то короткий синтаксис './array' здесь не работает:
// Magento тогда ищет скрипт по пути: pub/static/frontend/Magento/luma/en_US/array.js
define([
	'df-lodash'
	,'Df_Core/js/array'
	,'Df_Core/js/object'
	,'Df_Core/js/string'
	,'mage/translate'
], function(_, array, object, string, $t) {
	return {
		a: array
		/**
		 * 2016-08-05
		 * http://stackoverflow.com/a/894877
		 * @param {*} value
		 * @param {*} _default
		 * @returns {*}
		 */
		,arg: function(value, _default) {return this.d(value) ? value : _default;}
		/**
		 * 2016-08-08
		 * https://lodash.com/docs#once
		 * https://github.com/lodash/lodash/blob/4.14.2-amd/once.js
		 * https://github.com/lodash/lodash/blob/4.14.2-amd/before.js
 		 * @param {Function} func
		 * @returns {Function}
		 */
		,c: function(func) {return _.once(func);}
		/**
		 * 2016-08-08
		 * https://lodash.com/docs#memoize
		 * https://github.com/lodash/lodash/blob/4.14.2-amd/memoize.js#L50-L68
 		 * @param {Function} func
		 * @param {Function} resolver
		 * @returns {Function}
		 */
		,cp: function(func, resolver) {return _.memoize(func, resolver);}
		/**
		 * 2016-04-20
		 * @param {*} value
		 * @returns {Boolean}
		 */
		,d: function(value) {return 'undefined' !== typeof value;}
		,o: object
		,s: string
		/**
		 * 2016-08-07
		 * Замещает параметры аналогично моей функции PHP df_var()
		 * https://github.com/mage2pro/core/blob/1.5.23/Core/lib/text.php?ts=4#L913-L929
		 * @param {String} text
		 * @param {?Object|String} params [optional]
		 * @returns {String}
		 */
		,t: function(text, params) {return this.s.template($t(text), params);}
		/**
		 * 2016-04-20
		 * @param {*} value
		 * @returns {Boolean}
		 */
		,u: function(value) {return !this.d(value);}
	};
});