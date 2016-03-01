define(['jquery', 'underscore'], function($, _) {return {
	/**
	 * 2016-02-27
	 * Аналог функции PHP array_intersect_key()
	 * http://php.net/manual/function.array-intersect-key.php
	 *
	 * @param {Array} a1
	 * @param {Array} a2
	 * @returns {Array}
	 */
	intersectKeys: function(a1, a2) {
		// http://underscorejs.org/#keys
		// http://underscorejs.org/#intersection
		// http://underscorejs.org/#pick
		return _.pick(a1, _.intersection(_.keys(a1), _.keys(a2)));
	}
};});