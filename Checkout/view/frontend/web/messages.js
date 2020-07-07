// 2016-07-14
define(['jquery', 'Magento_Ui/js/model/messageList'], function($, ml) {return (
	/**
	 * @param {Object} cfg
	 * @param {Object[]} cfg.messages
	 * @returns void
	 */
	function(cfg) {$.each(cfg.messages, function() {
		/**
		 * @type {Object} this
		 * @type {String} this.text
		 * @type {Boolean} this.success
		 */
		/** @type {Function} fn */
		var fn = this.success ? ml.addSuccessMessage : ml.addErrorMessage;
		fn.call(ml, {'message': this.text});
	});});
});