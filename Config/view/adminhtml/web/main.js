require(['jquery', 'domReady!'], function($) {
	if (-1 !== location.href.indexOf('system_config/edit/section/dfe_')) {
		$('body').addClass('dfe-config');
	}
});