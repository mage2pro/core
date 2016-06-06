define(['jquery', 'underscore'], function($, _) {return {
	/**
	 * 2016-06-03
	 * Аналог моей функции PHP df_clean()
	 * @param {Array} a
	 * @returns {Array}
	 * http://underscorejs.org/#without
	 */
	clean: function(a) {return _.without(a, '', null, undefined, []);},
	/**
	 * 2016-02-27
	 * Аналог функции PHP array_intersect_key()
	 * http://php.net/manual/function.array-intersect-key.php
	 * http://underscorejs.org/#keys
	 * http://underscorejs.org/#intersection
	 * http://underscorejs.org/#pick
	 * @param {Array} a1
	 * @param {Array} a2
	 * @returns {Array}
	 */
	intersectKeys: function(a1, a2) {return _.pick(a1, _.intersection(_.keys(a1), _.keys(a2)));}
};});