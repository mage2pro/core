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
		var $setup = $('select.df-setup', $fonts);
		var updateSetupStatus = function() {
			debugger;
			/** @type {jQuery} HTMLSelectElement */
			var $this = $(this);
			// 2015-11-19
			// Приводить к логическому типу обязательно,
			// иначе «1» воспринимается как продолжительность анимации:
			// http://api.jquery.com/toggle/#toggle-display
			// Также !!$this.val() почему-то не работает.
			$this.closest('.field').siblings().toggle(0 !== parseInt($this.val()));
		};
		$setup.each(updateSetupStatus);
		$setup.change(updateSetupStatus);
	})();
});