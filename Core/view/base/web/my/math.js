// 2019-08-28
define([], function() {return {
	/**
	 * 2019-08-28
	 * @param {Number} v
	 * @param {Number} min
	 * @param {Number} max
	 * @returns {String}
	 */
	minmax: function(v, min, max) {return Math.min(max, Math.max(min, v));},
	/**
	 * 2019-08-28 https://stackoverflow.com/a/12830454
	 * @param {Number} v
	 * @param {Number} precision
	 * @returns {String}
	 */
	round: function(v, precision) {return +parseFloat(v).toFixed(precision);}
};});