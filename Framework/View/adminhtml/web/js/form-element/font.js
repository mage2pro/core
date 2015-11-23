debugger;
define(['jquery', 'domReady!'], function($) {return (
	/**
	 * 2015-11-23
	 * @param {Object} config
	 * @param {String} config.id
	 */
	function(config) {
		/** @type {jQuery} HTMLFieldSetElement */
		var $font = $(document.getElementById(config.id));
		/** @type {jQuery} HTMLSelectElement */
		var $setup = $('input.df-setup', $font);
		var updateSetupStatus = function() {
			/** @type {jQuery} HTMLSelectElement */
			var $this = $(this);
			$this.closest('.field').siblings().toggleClass('df-hidden', !$this.is(':checked'));
		};
		$setup.each(updateSetupStatus);
		$setup.change(updateSetupStatus);
	}
);});