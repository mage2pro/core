define(['df-lodash', 'df-uniform', 'jquery'], function(_, uniform, $) {return {
	/**
	 * 2016-08-08
	 * A way to handle a circular dependency: http://requirejs.org/docs/api.html#circular
	 * @returns {Object}
	 */
	df: function() {return require('df');},
	/**
	 * 2016-08-12
	 * http://stackoverflow.com/a/6969486
	 * @param {String} s
	 * @returns {String}
	 */
	escapeRE: function(s) {return s.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");},
	/**
	 * 2014-08-24
	 * 'Dfe_AllPay' => ['Dfe_', 'All', 'Pay']
	 * 'Dfe_ALLPay' => ['Dfe_', 'A', 'L', 'L', 'Pay']
	 * Почти аналогично PHP df_explode_camel():
	 * https://github.com/mage2pro/core/blob/1.7.5/Core/lib/text.php?ts=4#L225-L234
	 * http://stackoverflow.com/a/20381799
	 * @type {Function}
	 * @param {String} s
	 * @returns {String[]}
	 */
	explodeCamel: uniform(function(s) {return s.split(/(?=[A-Z])/g);}),
	/**
	 * 2014-08-24
	 * 'Dfe_AllPay' => ['Dfe', 'All', 'Pay']
	 * 'Dfe_ALLPay' => ['Dfe', 'A', 'L', 'L', 'Pay']
	 * @type {Function}
	 * @param {String} s
	 * @returns {String[]}
	 */
	explodeModuleCamel: function(s) {return this.explodeCamel(s.split('_'));},
	/**
	 * 2015-11-02
	 * http://stackoverflow.com/a/7616484
	 * @param {String} s
	 * @returns {Number}
	 */
	hash: function(s) {
		/** @type {Number} */ var result = 0;
		if (s) {
			/** @type {Number} */ var i;
			/** @type {Number} */ var len;
			for (i = 0, len = s.length; i < len; i++) {
				/** @type {Number} */ var chr = s.charCodeAt (i);
				result = ((result << 5) - result) + chr;
				result |= 0; // Convert to 32bit integer
			}
		}
		return result;
	},
	/**
	 * 2014-08-24
	 * Аналогично PHP df_lcfirst().
	 * http://stackoverflow.com/a/1026087
	 * @type {Function}
	 * @param {String|String[]} s
	 * @returns {String|String[]}
	 */
	lcFirst: uniform(function(s) {return s.charAt(0).toLowerCase() + s.slice(1);}),
	/**
	 * 2017-09-07
	 * @used-by https://github.com/mage2pro/phone/blob/1.0.11/view/base/web/validator.js#L3-L8
	 * @used-by Dfe_Qiwi/main::dfData()
	 * https://github.com/mage2pro/qiwi/blob/1.0.4/view/frontend/web/main.js#L8-L19
	 * 2017-10-22
	 * @see df_phone_format_clean() https://github.com/mage2pro/phone/blob/1.0.11/lib/main.php#L67-L73
	 * @param {String} s
	 * @returns {String}
	 */
	normalizePhone: function (s) {return '+' + s.replace(/[\s+\-()]/g, '');},
	/**
	 * 2015-11-04
	 * Вставляет подстроку newChunk в заданное место position строки string.
	 * http://stackoverflow.com/a/4314050
	 * http://stackoverflow.com/a/4314044
	 * @param {String} string
	 * @param {Number} position
	 * @param {String} newChunk
	 * @returns {String}
	 */
	splice: function(string, position, newChunk) {
		return string.slice(0, position) + newChunk + string.slice(position);
	},
	/**
	 * 2016-08-07
	 * Замещает параметры аналогично моей функции PHP df_var()
	 * https://github.com/mage2pro/core/blob/1.5.23/Core/lib/text.php?ts=4#L913-L929
	 *
	 * 2016-08-08
	 * Lodash содержит функцию template: https://lodash.com/docs#template
	 * Я не использую её, потому что она слишком навороченная для моего случая.
	 *
	 * JSFiddle: https://jsfiddle.net/dfediuk/uxusbhes/1/
	 *
	 * @param {String} result
	 * @param {Object|String|Array=} params
	 * @returns {String}
	 */
	t: function(result, params) {
		params = this.df().arg(params, {});
		// 2016-08-08 Simple — не массив и не объект.
		/** @type {Boolean} */ var paramsIsSimple = !_.isObject(params);
		// 2016-08-07 Поддерживаем сценарий df.t('One-off Payment: %s.');
		if (paramsIsSimple && 2 === arguments.length) {
			result = result.replace('%s', params).replace('{0}', params);
		}
		else {
			if (paramsIsSimple) {
				// 2016-08-08
				// Почему-то прямой вызов arguments.slice(1) приводит к сбою:
				// «arguments.slice is not a function».
				// Решение взял отсюда: http://stackoverflow.com/a/960870
				params = Array.prototype.slice.call(arguments, 1);
			}
			// 2016-08-08
			// params теперь может быть как объектом, так и строкой: алгоритм един.
			// http://api.jquery.com/jquery.each/
			_.each(params, function(value, name) {
				result = result.replace('{' + name + '}', value);
			});
		}
		return result;
	},
	/**
	 * 2017-02-16
	 * @type {Function}
	 * @param {String|String[]} s
	 * @returns {String|String[]}
	 */
	ucFirst: uniform(function(s) {return s.charAt(0).toUpperCase() + s.slice(1);}),
	/**
	 * 2016-06-03
	 * Возвращает случайную уникальную строку.
	 * Аналог функции PHP df_uid()
	 * http://stackoverflow.com/a/105074
	 * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Math/pow
	 *
	 * 2016-08-08
	 * Сейчас никем не используется.
	 *
	 * @param {Number=} length
	 * @param {String=} prefix
	 * @returns {String}
	 */
	uid: function(length, prefix) {
		// http://stackoverflow.com/questions/894860
		length = this.df().arg(length, 4);
		prefix = this.df().arg(prefix, '');
		return prefix + (Math.floor((1 + Math.random()) * Math.pow(16, length + 1))
			.toString(16)
			// отсекаем первый символ, потому что он всегда равен «1»
			.substring(1)
		);
	}
};});
