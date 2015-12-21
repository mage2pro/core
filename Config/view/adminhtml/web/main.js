require(['jquery', 'domReady!'], function($) {
	if (-1 !== location.href.indexOf('system_config/edit/section/dfe_')) {
		$('body').addClass('dfe-config');
	}
	/**
	 * 2015-12-21
	 * Формы не передают на сервер выключенные чекбоксы.
	 * http://stackoverflow.com/questions/3029870
	 */
	var $form = $('form#config-edit-form');
	$('input[type=checkbox].df-checkbox', $form).each(function() {
		var $this = $(this);
		var $label = $this.closest('tr').children('td.label').children('label');
		$label.hover(
			function() {$(this).addClass('df-hover');}
			, function() {$(this).removeClass('df-hover');}
		);
	});
	$form.submit(function() {
		var $form = $(this);
		$('input[type=checkbox]:not(:checked)', $form).each(function() {
			$form.append($('<input>').attr({
				type: 'hidden'
				,name: this.name
				,value: 0
			}));
		});
	});
});