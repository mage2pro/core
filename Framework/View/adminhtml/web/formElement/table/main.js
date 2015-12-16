define(['jquery', 'Df_Core/Handsontable', 'domReady!'], function($) {return (
	/**
	 * 2015-11-24
	 * @param {Object} config
	 * @param {String} config.id
	 * @param {String[]} config.columns
	 */
	function(config) {
		var $element = $(document.getElementById(config.id));
		var $container = $('<div id="test"/>');
		$element.after($container);
		//debugger;
		var data = [
			['1', '2', '3']
			, ['4', '5', '6']
		];
		//data.unshift(config.columns);
		new Handsontable($container.get(0), {
			data: data
			// 2015-12-16
			// http://docs.handsontable.com/0.20.2/Options.html#colHeaders
			,colHeaders: config.columns
		});
	}
);});