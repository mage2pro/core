define([
	'./Df_Core/js/array'
	, './Df_Core/js/string'
], function(array, string) {
	return {
		array: array
		/**
		 * 2016-04-20
		 * @param value
		 * @returns {Boolean}
		 */
		,defined: function(value) {return 'undefined' !== typeof value;}
		, string: string
		/**
		 * 2016-04-20
		 * @param value
		 * @returns {Boolean}
		 */
		,undefined: function(value) {return !this.defined(value);}
	};
});