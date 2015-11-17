require(['jquery', 'domReady!'], function($) {
	/** @type {jQuery} HTMLFieldSetElement */
	var $fieldsets = $('.df-fieldset');
	$('.df-checkbox', $fieldsets).closest('.field').addClass('df-checkbox');
});