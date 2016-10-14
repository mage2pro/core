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
	,'mage/translate'
], function(_, array, dom, money, object, string, $t) {return {
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
	 * @param {*} value
	 * @returns {Boolean}
	 */
	,d: function(value) {return 'undefined' !== typeof value;}
	,dom: dom
	,money: money
	,o: object
	,s: string
	/**
	 * 2016-08-07
	 * Замещает параметры аналогично моей функции PHP df_var()
	 * https://github.com/mage2pro/core/blob/1.5.23/Core/lib/text.php?ts=4#L913-L929
	 * @param {String} text
	 * @param {Object|String=} params [optional]
	 * @returns {String}
	 */
	,t: function(text, params) {return this.s.t($t(text), params);}
	/**
	 * 2016-04-20
	 * @param {*} value
	 * @returns {Boolean}
	 */
	,u: function(value) {return !this.d(value);}
};});