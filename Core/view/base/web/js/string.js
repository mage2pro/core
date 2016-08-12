define(['df-lodash', 'jquery'], function(_, $) {return {
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
	escapeRE: function(s) {
		return s.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
	},
	/**
	 * 2015-11-02
	 * http://stackoverflow.com/a/7616484
	 * @param {String} string
	 * @returns {Number}
	 */
	hash: function(string) {
		/** @type {Number} */
		var result = 0;
		if (string.length) {
			/** @type {Number} */
			var i;
			/** @type {Number} */
			var len;
			for (i = 0, len = string.length; i < len; i++) {
				/** @type {Number} */
				var chr = string.charCodeAt (i);
				result = ((result << 5) - result) + chr;
				result |= 0; // Convert to 32bit integer
			}
		}
		return result;
	},
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
	 * @param {?Object|String|Array} params [optional]
	 * @returns {String}
	 */
	t: function(result, params) {
		params = this.df().arg(params, {});
		/**
		 * 2016-08-08
		 * Simple — не массив и не объект.
		 * @type {Boolean}
		 */
		var paramsIsSimple = !_.isObject(params);
		// 2016-08-07
		// Поддерживаем сценарий df.t('One-off Payment: %s.');
		if (paramsIsSimple && 2 === arguments.length) {
			result = result.replace('%s', params);
		}
		else {
			if (paramsIsSimple) {
				/**
				 * 2016-08-08
				 * Почему-то прямой вызов arguments.slice(1) приводит к сбою:
				 * «arguments.slice is not a function».
				 * Решение взял отсюда: http://stackoverflow.com/a/960870
				 */
				params = Array.prototype.slice.call(arguments, 1);
			}
			/**
			 * 2016-08-08
			 * params теперь может быть как объектом, так и строкой: алгоритм един.
			 * http://api.jquery.com/jquery.each/
			 */
			$.each(params, function(name, value) {
				result = result.replace('{' + name + '}', value);
			});
		}
		return result;
	},
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
	 * @param {Number} length [optional]
	 * @param {String} prefix [optional]
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