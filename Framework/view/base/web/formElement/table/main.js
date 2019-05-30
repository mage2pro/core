define(['jquery', 'Df_Core/Handsontable', 'domReady!'], function($) {return (
	/**
	 * 2015-11-24
	 * @param {Object} config
	 * @param {String} config.id
	 * @param {String[]} config.columns
	 */
	function(config) {
		var $element = $(document.getElementById(config.id));
		var $container = $('<div class="df-table"/>');
		$element.after($container);
		var $table = new Handsontable($container.get(0), {
			cell: []
			,data:
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
						/**
						 * 2016-01-10
						 * Пример массива с данными для Handsontable из 3 колонок и 5 строк:
						 [
						 	["тест","2","3"]
						 	,["4","5","6"]
						 	,["","тест","12"]
						 	,["7","999","9"]
						 	,["88888",null,null]
						 ]
						 * Когда мы создаём пустой массив, приходится делать хотя бы одну строку,
						 * иначе Handsontable работает некорректно.
						 */
						result = [new Array(config.columns.length)];
					}
					return result;
				}()
			// 2015-12-16
			// http://docs.handsontable.com/0.20.2/Options.html#colHeaders
			,colHeaders: config.columns
			// 2015-12-16
			// Позволяет переупорядочивать строки таблицы мышкой.
			// http://docs.handsontable.com/0.20.2/demo-moving-rows-and-columns.html
			// http://docs.handsontable.com/0.20.2/Options.html#manualRowMove
			,manualRowMove: true
			// 2015-12-16
			// Похоже, мы нуждаемся в этой опции,
			// чтобы при нажатии кнопки «Delete» мы могли использовать $table.getSelected()
			// http://stackoverflow.com/a/17389359
			,outsideClickDeselects: false
			// 2015-12-16
			// Вынуждены включать, чтобы работало manualRowMove
			// http://docs.handsontable.com/0.20.2/demo-moving-rows-and-columns.html
			,rowHeaders: true
			// 2015-12-16
			// Растягивает таблицу по ширине родительского контейнера.
			// Значение «all» означает, что все колонки растягиваются равномерно.
			// http://docs.handsontable.com/0.20.2/Options.html#stretchH
			// https://code.dmitry-fedyuk.com/discourse/df-table/blob/330c130a98a4e4bc26ef855ffcda401726ba1b33/assets/javascripts/models/editor.js.es6#L41
			,stretchH: 'all'
		});
		(function() {
			/** @type {jQuery} HTMLFormElement */
			var $form = $element.closest('form');
			// 2015-12-16
			// By analogy with https://github.com/mage2pro/markdown/blob/d030a44b/view/adminhtml/web/main.js#L364
			$form.bind('beforeSubmit', function() {
				$element.val(JSON.stringify($table.getData()));
			});
		})();
		(function() {
			var $toolbar = $('<div/>').addClass('toolbar');
			$container.after($toolbar);
			var button = function(title, onClick) {
				var $result = $('<button>');
				$result.append($('<span/>').append(title));
				$result.click(function(event) {
					event.preventDefault();
					onClick();
				});
				$toolbar.append($result);
				return $result;
			};
			button('Add', function() {
				// http://stackoverflow.com/a/29427470
				// Если порядковый номер добавляемой строки вторым параметром не указан,
				// то строка добавляется внизу таблицы.
				// https://github.com/handsontable/handsontable/issues/290#issuecomment-14597650
				$table.alter('insert_row');
			});
			button('Delete', function() {
				// http://stackoverflow.com/a/17389359
				$table.alter('remove_row', $table.getSelected()[0]);
			});
		})();
	}
);});