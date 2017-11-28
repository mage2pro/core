// 2016-08-08
// Почему-то короткий синтаксис './array' здесь не работает:
// Magento тогда ищет скрипт по пути: pub/static/frontend/Magento/luma/en_US/array.js
define([
	'df-lodash'
	,'Df_Core/my/array'
	,'Df_Core/my/dom'
	,'Df_Core/my/money'
	,'Df_Core/my/object'
	,'Df_Core/my/string'
	,'Df_Intl/t'
], function(_, array, dom, money, object, string, $t) {return {
	a: array
	/**
	 * 2016-08-05
	 * http://stackoverflow.com/a/894877
	 * @param {*} v
	 * @param {*} d
	 * @returns {*}
	 */
	,arg: function(v, d) {return this.d(v) ? v : d;}
	/**
	 * 2016-08-08
	 * https://lodash.com/docs#once
	 * https://github.com/lodash/lodash/blob/4.14.2-amd/once.js
	 * https://github.com/lodash/lodash/blob/4.14.2-amd/before.js
	 * 2016-08-22
	 * Эта функция некорректно работает при наследовании вызывающего её класса:
	 * классы-наследники будут разделять кэш между собой.
	 * Правильным решением в данном случае будет описание своей кэширующей функции
	 * в родительском классе с использованием _.memoize
	 * и с расчётом ключа кэша в параметре resolver.
	 * Смотрите пример правильной реализации в
	 * mage2pro/core/Payment/view/frontend/web/js/view/payment/mixin.js
	 * @param {Function} func
	 * @returns {Function}
	 */
	,c: function(func) {return _.once(func);}
	/**
	 * 2016-06-03
	 * Аналог моей функции PHP df_clean()
	 * http://underscorejs.org/#without
	 *
	 * 2016-08-08
	 * В библиотеках Underscope и Lodash есть ещё функция compact,
	 * но она удаляет больше, чем наша: все значения, которые приводятся к false
	 * (в том числе false и 0, что нам не надо).
	 * http://underscorejs.org/#compact
	 * https://lodash.com/docs#compact
	 *
	 * В Lodash тоже есть такая функция: https://lodash.com/docs#without
	 *
	 * JSFiddle: https://jsfiddle.net/dfediuk/fj0shdte/
	 *
	 * @used-by Dfe_Stripe/main::tokenParams()
	 * @param {Array|Object} ao
	 * @returns {Array|Object}
	 */
	,clean: function(ao) {
		/** @type {Array} */
		var valuesToClean = ['', null, undefined, []];
		/** @type {Array|Object} */
		var result;
		if (_.isArray(ao)) {
			// 2016-08-08
			// http://stackoverflow.com/a/8708978
			result = _.difference(ao, valuesToClean);
		}
		else {
			// 2016-08-08
			// http://stackoverflow.com/a/14058408
			result = _.clone(ao);
			_.forIn(result, function(value, key) {
				if (-1 < valuesToClean.indexOf(value)) {
					delete result[key];
				}
			});
		}
		return result;
	}
	/**
	 * 2016-08-08
	 * https://lodash.com/docs#memoize
	 * https://github.com/lodash/lodash/blob/4.14.2-amd/memoize.js#L50-L68
	 * 2016-08-22
	 * Эта функция некорректно работает при наследовании вызвавающего её класса:
	 * классы-наследники будут разделять кэш между собой.
	 * Правильным решением в данном случае будет описание своей кэширующей функции
	 * в родительском классе с использованием _.memoize
	 * и с расчётом ключа кэша в параметре resolver.
	 * Смотрите пример правильной реализации в
	 * mage2pro/core/Payment/view/frontend/web/js/view/payment/mixin.js
	 * @param {Function} func
	 * @param {Function} resolver
	 * @returns {Function}
	 */
	,cp: function(func, resolver) {return _.memoize(func, resolver);}
	/**
	 * 2016-04-20
	 * @param {*} v
	 * @returns {Boolean}
	 */
	,d: function(v) {return 'undefined' !== typeof v;}
	,dom: dom
	/**
	 * 2017-07-15
	 * It is a shortcut to @uses parseInt().
	 * Mozilla Developer Network suggests always specify the radix:
	 * «Always specify this parameter to eliminate reader confusion and to guarantee predictable behavior.
	 * Different implementations produce different results when a radix is not specified,
	 * usually defaulting the value to 10.»
	 * https://developer.mozilla.org/en/docs/Web/JavaScript/Reference/Global_Objects/parseInt#Parameters
	 * @param {String} v
	 * @returns {Number}
	 */
	,int: function(v) {return parseInt(v, 10);}
	/**
	 * 2017-07-30
	 * @returns {Boolean}
	 */
	,isLocalhost: function () {return -1 !== window.location.hostname.indexOf('localhost');}
	/**
	 * 2017-03-08
	 * @used-by tr()
	 * @param {Object} o
	 * @param {String} k
	 * @param {*} d
	 * @returns {*}
	 */
	,k: function(o, k, d) {return this.d(o[k]) ? o[k] : d;}
	,money: money
	,o: object
	,s: string
	/**
	 * 2016-08-07
	 * Замещает параметры аналогично моей функции PHP df_var()
	 * https://github.com/mage2pro/core/blob/1.5.23/Core/lib/text.php?ts=4#L913-L929
	 * @param {String} text
	 * @param {Object|String=} params
	 * <дополнительные параметры>
	 * @returns {String}
	 */
	,t: function(text, params) {
		/** @type {Array} */
		var a = Array.prototype.slice.call(arguments, 1);
		a.unshift($t(text));
		// 2016-08-08
		// Почему-то прямой вызов arguments.slice(1) приводит к сбою:
		// «arguments.slice is not a function».
		// Решение взял отсюда: http://stackoverflow.com/a/960870
		return this.s.t.apply(this.s, a);
	}
	/**
	 * 2017-03-08
	 * @param {String} v
	 * @param {Object} $map
	 * @return {String}
	 */
	,tr: function(v, $map) {return this.k($map, v, v);}
	/**
	 * 2016-04-20
	 * @param {*} v
	 * @returns {Boolean}
	 */
	,u: function(v) {return !this.d(v);}
};});
