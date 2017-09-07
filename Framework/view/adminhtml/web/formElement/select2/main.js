define(['df', 'jquery', 'Df_Core/Select2', 'domReady!'], function(df, $) {return (
	/**
	 * 2016-08-10
	 * @param {Object} config
	 * @param {String} config.id
	 * @param {String} config.cssClass
	 * @param {Array} config.options
	 * @param {String} config.value		Выбранное значение
	 * @param {?String} config.width
	 */
	function(config) {
		var prepare = function($element) {
			$element.select2({
				data: $.map(config.options,
					/**
					 * 2016-08-10
					 * @param {Object} option
					 * @param {String} option.label
					 * @param {String} option.value
					 * @returns {Object}
					 */
					function(option) {return {id: option.value, text: option.label};}
				)
				/**
				 * 2015-12-11
				 * https://github.com/select2/select2/blob/4.0.1/dist/js/select2.full.js#L5601
				 * http://select2.github.io/select2/#documentation
				 * 2016-08-10
				 * Это — стиль выпадающего списка,
				 * который иерархически который расположен снаружи основного DOM,
				 * внизу страницы.
				 */
				,dropdownCssClass: config.cssClass
				// 2016-08-10 Скрываем поле поиска: http://stackoverflow.com/a/17649822
				,minimumResultsForSearch: -1
			});
			if (config.value) {
				$element.val(config.value).change(); // http://stackoverflow.com/a/30477163
			}
			// 2016-08-10
			// http://stackoverflow.com/a/32692811
			$element.data('select2').$container.addClass(config.cssClass);
			if (config.width) {
				$element.data('select2').$container.css('width', config.width);
			}
		};
		/** @type {jQuery} HTMLSelectElement */
		var $element = $(document.getElementById(config.id));
		if (-1 === config.id.indexOf('[template]')) {
			prepare($element);
		}
		else {
			$(window).bind('df.config.array.add', function(event, $container) {
				var re = new RegExp(
					df.s.escapeRE(
						config.id.replace('[template]', '[#template#]')
					).replace('#template#', '\\d+')
				);
				$('select', $container).filter(function() {
					return this.id.match(re);
				}).each(function() {
					prepare($(this));
				});
			})
		}
	}
);});