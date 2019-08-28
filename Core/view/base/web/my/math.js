// 2019-08-28
define([], function() {return {
	/**
	 * 2019-08-28
	 * @used-by https://github.com/inkifi/map/blob/0.0.8/view/frontend/web/js/create.js#L50
	 * @param {Number} v
	 * @param {Number} min
	 * @param {Number} max
	 * @returns {String}
	 */
	minmax: function(v, min, max) {return Math.min(max, Math.max(min, v));},
	/**
	 * 2019-08-28 https://stackoverflow.com/a/12830454
	 * @used-by https://github.com/inkifi/map/blob/0.0.8/view/frontend/web/js/create.js#L50
	 * @param {Number} v
	 * @param {Number} precision
	 * @returns {String}
	 */
	round: function(v, precision) {return +parseFloat(v).toFixed(precision);}
};});