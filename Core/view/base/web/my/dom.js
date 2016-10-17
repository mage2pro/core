define(['Df_Core/my/string'], function(s) {return {
	/**
	 * 2016-08-08
	 * @param {String} name
	 * @param {String} value
	 * @returns {String}
	 */
	attr: function(name, value) {return s.t('[{0}="{1}"]', name, value)},
	/**
	 * 2016-08-08
	 * http://stackoverflow.com/a/8622351
	 * @param {jQuery} $buttons HTMLInputElement[]
	 * @returns {?String}
	 */
	radioValue: function($buttons) {
		/** @type {jQuery} HTMLInputElement[] */
		var selected = $buttons.filter(':checked');
		return selected.length ? selected.val() : null;
	}
};});