require(['jquery', 'domReady!'], function($) {
	/** @type {jQuery} HTMLFieldSetElement[] */
	var $fieldsets = $('.df-fieldset');
	/** @type {jQuery} HTMLInputElement[] */
	var $checkboxes = $('.df-checkbox', $fieldsets);
	$checkboxes.closest('.field').addClass('df-checkbox');
	$checkboxes.closest('.df-fieldset-inline').addClass('df-checkbox');
	(function(){
		/** @type {jQuery} HTMLFieldSetElement[] */
		var $fonts = $fieldsets.filter('.df-font');
		/** @type {jQuery} HTMLSelectElement[] */
		var $setup = $('.df-setup', $fonts);
		var updateSetupStatus = function() {
			/** @type {jQuery} HTMLSelectElement */
			var $this = $(this);
			$this.closest('.field').siblings().toggle($this.is(':checked'));
		};
		$setup.each(updateSetupStatus);
		$setup.change(updateSetupStatus);
	})();
});