define(['jquery', 'Df_Core/Select2', 'domReady!'], function($) {return (
	/**
	 * 2016-03-08
	 * @param {Object} config
	 * @param {String} config.id
	 * @param {String} config.dataSource
	 * @param {String} config.value		Выбранное значение
	 */
	function(config) {
		/** @type {jQuery} HTMLSelectElement */
		var $element = $(document.getElementById(config.id));
		$element.select2({
			// 2016-04-13
			// http://stackoverflow.com/a/21198924
			width: '100%'
		});
	}
);});