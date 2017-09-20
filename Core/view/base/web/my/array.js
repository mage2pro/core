define(['jquery', 'df-lodash'], function($, _) {return {
	/**
	 * 2016-08-06
	 * Аналог моей функции PHP df_ccc()
	 * @used-by Df_Payment/withOptions::containerCss()
	 * @param {String} glue
	 * @param {Array} a
	 * @returns {String}
	 */
	ccClean: function(glue, a) {return this.df().clean(a).join(glue);},
	/**
	 * 2017-04-28 https://gist.github.com/miguelmota/300445a06a342e47a335
	 * @param {Array} a
	 * @returns {Array}
	 */
	clone: function(a) {return _.map(a, _.clone);},
	/**
	 * 2016-08-08
	 * A way to handle a circular dependency: http://requirejs.org/docs/api.html#circular
	 * @returns {Object}
	 */
	df: function() {return require('df');}
};});