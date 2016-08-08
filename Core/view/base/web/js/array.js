define(['jquery', 'df-lodash'], function($, _) {return {
	/**
	 * 2016-08-06
	 * Аналог моей функции PHP df_cc_clean()
	 * @param {String} glue
	 * @param {Array} a
	 * @returns {String}
	 */
	ccClean: function(glue, a) {return this.df().clean(a).join(glue);},
	/**
	 * 2016-08-08
	 * A way to handle a circular dependency: http://requirejs.org/docs/api.html#circular
	 * @returns {Object}
	 */
	df: function() {return require('df');}
};});