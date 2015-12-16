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
		var $table = new Handsontable($container.get(0), {
			data:
				/** @returns {Array} */
				function() {
					/** @type {String} */
					var valuesS = $element.val();
					/** @type {Array} */
					var result = null;
					if ('' !== valuesS) {
						//noinspection UnusedCatchParameterJS,EmptyCatchBlockJS
						try {result = JSON.parse(valuesS);} catch(e) {}
					}
					if (!$.isArray(result)) {
						result = [];
					}
					return result;
				}()
			// 2015-12-16
			// http://docs.handsontable.com/0.20.2/Options.html#colHeaders
			,colHeaders: config.columns
			// 2015-12-16
			// http://docs.handsontable.com/0.20.2/demo-moving-rows-and-columns.html
			// http://docs.handsontable.com/0.20.2/Options.html#manualRowMove
			//
			// Похоже, что manualRowMove не обновляет источник данных:
			// http://stackoverflow.com/a/27625311/
			,manualRowMove: true
			// 2015-12-16
			// Вынуждены включать, чтобы работало manualRowMove
			// http://docs.handsontable.com/0.20.2/demo-moving-rows-and-columns.html
			,rowHeaders: true
		});
		/** @type {jQuery} HTMLFormElement */
		var $form = $element.closest('form');
		/**
		 * 2015-12-16
		 * По аналогии с http://code.dmitry-fedyuk.com/m2e/markdown/blob/d030a44bfe75765d54d68acf106e2fbb9bd66b4c/view/adminhtml/web/main.js#L364
		 */
		$form.bind('beforeSubmit', function() {
			$element.val(JSON.stringify($table.getData()));
		});
	}
);});