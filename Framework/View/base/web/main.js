require(['jquery', 'domReady!'], function($) {
	/** @type {jQuery} HTMLFieldSetElement */
	var $fieldsets = $('.df-fieldset');
	debugger;
	$('.df-checkbox', $fieldsets).closest('.field').addClass('df-checkbox');
});