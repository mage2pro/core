define([], function() {return {
	/**
	 * 2016-08-26
	 * Возвращает строку из 2 последних цифр денежной величины.
	 * @param {Number} amount
	 * @returns {String}
	 */
	last2: function(amount) {
		/** @type {String} */
		var amountS = Math.round(100 * amount).toString();
		return amountS.substring(amountS.length - 2);
	}
};});