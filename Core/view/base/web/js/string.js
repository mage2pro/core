define([], function() {return {
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
	}
};});