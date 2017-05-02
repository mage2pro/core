define(['jquery', 'df-lodash'], function($, _) {return (
	/**
	 * 2016-08-24
	 * @param {Function} func
	 * @returns {Function}
	 */
	function(func) {return (
		/**
		 * @param {*|[]} s
		 * @returns {*|[]}
		 */
		function(s) {return $.isArray(s) ? $.map(s, func) : func(s);}
	);}
);});