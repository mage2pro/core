require(['jquery', 'domReady!'], function($) {
	/** @type {jQuery} HTMLFieldSetElement[] */
	var $fieldsets = $('.df-fieldset');
	(function(){
		/** @type {jQuery} HTMLFieldSetElement[] */
		var $fonts = $fieldsets.filter('.df-font');
		/** @type {jQuery} HTMLSelectElement[] */
		var $setup = $('input.df-setup', $fonts);
		var updateSetupStatus = function() {
			/** @type {jQuery} HTMLSelectElement */
			var $this = $(this);
			$this.closest('.field').siblings().toggle($this.is(':checked'));
		};
		$setup.each(updateSetupStatus);
		$setup.change(updateSetupStatus);
	})();
});