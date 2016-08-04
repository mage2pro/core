// 2016-08-04
define (['./createMessagesComponent'], function(createMessagesComponent) {return {
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
	defaults: {active: false},
	/** @returns {String} */
	getCode: function() {return this.code;},
	imports: {onActiveChange: 'active'},
	/**
	 * 2016-08-04
	 * @return {Boolean}
	*/
	isTest: function() {return this.config('isTest');}
};});
