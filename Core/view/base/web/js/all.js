define([
	'./Df_Core/js/array'
	,'./Df_Core/js/string'
	,'mage/translate'
], function(array, string, $t) {
	return {
		/**
		 * 2016-08-05
		 * @param {*} value
		 * @param {*} _default
		 * @returns {*}
		 */
		arg: function(value, _default) {return this.defined(value) ? value : _default;}
		,array: array
		/**
		 * 2016-04-20
		 * @param {*} value
		 * @returns {Boolean}
		 */
		,defined: function(value) {return 'undefined' !== typeof value;}
		,string: string
		/**
		 * 2016-08-07
		 * Замещает параметры аналогично моей функции PHP df_var()
		 * https://github.com/mage2pro/core/blob/1.5.23/Core/lib/text.php?ts=4#L913-L929
		 * @param {String} text
		 * @param {?Object|String} params [optional]
		 * @returns {String}
		 */
		,t: function(text, params) {return this.string.template($t(text), params);}
		/**
		 * 2016-04-20
		 * @param {*} value
		 * @returns {Boolean}
		 */
		,undefined: function(value) {return !this.defined(value);}
		/**
		 * 2016-06-03
		 * Возвращает случайную уникальную строку.
		 * Аналог функции PHP df_uid()
		 * http://stackoverflow.com/a/105074
		 * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Math/pow
		 * @param {Number} length [optional]
		 * @param {String} prefix [optional]
		 * @returns {String}
		 */
		,uid: function(length, prefix) {
			// http://stackoverflow.com/questions/894860
			length = this.defined(length) ? length : 4;
			prefix = this.defined(prefix) ? prefix : '';
			return prefix + (Math.floor((1 + Math.random()) * Math.pow(16, length + 1))
				.toString(16)
				// отсекаем первый символ, потому что он всегда равен «1»
				.substring(1)
			);
		}
	};
});