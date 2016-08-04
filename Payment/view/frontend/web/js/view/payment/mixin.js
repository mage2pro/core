// 2016-08-04
define ([
	'./createMessagesComponent', 'jquery', 'df'
], function(createMessagesComponent, $, df) {return {
	/**
	 * 2016-08-04
	 * @param {?String} key
	 * @returns {Object}|{*}
	 */
	config: function(key) {
		/** @type {Object} */
		var result =  window.checkoutConfig.payment[this.getCode()];
		return !key ? result : result[key];
	},
	createMessagesComponent: createMessagesComponent,
	/**
	 * 2016-08-04
	 * @return {String}
	*/
	debugMessage: function() {return '';},
	defaults: {active: false},
	/**
	 * 2016-08-04
	 * @param {?String} field [optional]
	 * @returns {jQuery}|{String}
	 */
	dfForm: function(field) {
		if (df.undefined(this._dfForm)) {
			this._dfForm = $('form.' + this.getCode());
		}
		return !field ? this._dfForm : $('[data="' + field + '"]', this._dfForm).val();
	},
	imports: {onActiveChange: 'active'},
	/**
	 * 2016-08-04
	 * @return {Boolean}
	*/
	isTest: function() {return this.config('isTest');}
};});
