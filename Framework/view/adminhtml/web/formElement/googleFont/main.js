define(['df', 'jquery', 'Df_Core/Select2', 'domReady!'], function(df, $) {return (
	/**
	 * 2015-11-28
	 * @param {Object} config
	 * @param {String} config.id
	 * @param {String} config.dataSource
	 * @param {String} config.value		Выбранное значение
	 */
	function(config) {
		/** @type {jQuery} HTMLSelectElement */
		var $element = $(document.getElementById(config.id));
		// 2015-11-28
		// https://select2.github.io/examples.html#responsive
		//$element.css('width', '100%');
		/**
		 * 2015-11-28
		 * Чтобы можно было делать такие асинхронные запросы к другому домену,
		 * я добавил в настройках Nginx:
		 * add_header 'Access-Control-Allow-Origin' '*';
		 * http://enable-cors.org/server_nginx.html
		 * https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS
		 */
		$element.append($('<option>').attr('selected', 'selected').html('Loading...'));
		/**
		 * 2015-12-06
		 * Обратите внимание, что у $.getJSON отсутствует timeout.
		 * Это нам на руку, потому что первичная генерация образцов шрифтов
		 * может занимать десятки секунд.
		 * http://stackoverflow.com/questions/14238619
		 */
		/** @type {Number} */
		var previewWidth = 300;
		/**
		 * 2015-12-10
		 * Изначально я делал высоту миниатюр равной $element.height(),
		 * однако это не совсем правильно, потому что $element.height() —
		 * это всего лишь высота исходного элемента управления до инициализации Select2.
		 *
		 * У моего Select2 высота равна 33 пикселя.
		 *
		 * При этом другое правило гласит «box-sizing: border-box;»:
		 * https://github.com/select2/select2/blob/4.0.1/dist/css/select2.css#L8
		 *
		 * Это значит, что указанная выше высота включает в себя border и padding.
		 * https://developer.mozilla.org/en/docs/Web/CSS/box-sizing#Values
		 * padding отсутствует, а border равен 1 пикселю сверху и снизу:
		 * https://github.com/select2/select2/blob/4.0.1/dist/css/select2.css#L130
		 *
		 * Поэтому нам надо делать миниатюры высотой 31 пиксель.
		 */
		/** @type {Number} */
		var previewHeight = 31;
		/**
		 * 2015-12-09
		 * На странице может быть сразу несколько данных элементов управления.
		 * Возникает проблема: как синхронизировать их одновременные обращения к серверу за данными?
		 * Проблемой это является по следующим причинам:
		 *
		 * 1) Генерация образцов шрифтов — длительная (порой минуты) задача
		 * со множеством файловых операций.
		 * Параллельный запуск сразу двух таких генераций
		 * (а они будут выполняться разными процессами PHP)
		 * почти наверняка приведёт к файловым конфликтам и ошибкам,
		 * да и вообще смысла в этом никакого нет:
		 * зачем параллельно делать одно и то же с одними и теми же объектами?
		 * Эта проблема была решена в серверной части применением функции df_sync:
		 * https://github.com/mage2pro/core/tree/554a47d4e06257bb9aa468e3b6a874eb6b808630/Api/Controller/Google/Fonts.php#L16
		 *
		 * 2) Однако описанная выше проблема не является единственной!
		 * Я так понял, как минимум в режиме разработчика
		 * присутствует ещё и общая проблема одновременной генерации классов и браузерных ресурсов.
		 * Порой это приводит к ответу сервера кодом HTTP 500 (Internal Server Error).
		 * В общем-то, как я понял, эта проблема возникает только в режиме разработчика
		 * и только при первичной генерации.
		 *
		 * 3) Есть ещё одна проблема: второй и последующие запросы за теми же данными
		 * просто ждут своей очереди посредством использования функции PHP usleep:
		 * https://github.com/mage2pro/core/tree/554a47d4e06257bb9aa468e3b6a874eb6b808630/Core/Sync.php#L77
		 * Т.е. они держат свои ресурсы (оперативную память и т.п.) и ждут.
		 * Это не очень хорошо.
		 *
		 * По причине 3 и отчасти причине 2 я теперь делаю
		 * синхронизацию запросов к серверу в браузере:
		 * запрос за данными делает только первый элемент управления,
		 * а остальные ждут его и пользуются его данными.
		 */
		/** @type {String} */
		var globalKey = 'df-google-font';
		/** @type {String} */
		var eventName = 'df-google-font';
		if (!window[globalKey]) {
			window[globalKey] = true;
			$.getJSON(config.dataSource, {
				width: previewWidth
				,height: previewHeight
				,fontColor: [48, 48, 48, 0].join('|')
				,fontSize: 14
			}, function(data) {$(window).trigger(eventName, data);})
			.fail(function() {$(window).trigger(eventName, []);});
		}
		$(window).bind(eventName, function(event, data) {
			if (!data['fonts']) {
				$element.children().html('Unable to load the data :-(');
			}
			else {
				$element.empty();
				/**
				 * 2015-12-10
				 * http://stackoverflow.com/a/33971933
				 * @param {Select2Item} item
				 * @param {Number=} marginBottom
				 * @returns {String}|{jQuery} HTMLElement
				 */
				var preview = function(item, marginBottom) {
					marginBottom = marginBottom || 0;
					/** @type {String}|{jQuery} HTMLElement */
					var result = item.text;
					// 2015-12-01
					// item.id не передаётся для первого псевдо-элемента «Searching...»
					/** @type {?Number[]} */
					var p = item.datumPoint;
					/**
					 * 2015-12-10
					 *
					 * Вообще говоря, нам желательнее было бы использовать не margin-bottom,
					 * а padding-bottom: чтобы отступ считался частью элемента и реагировал на клики.
					 * Однако Select2 использует для элемента правило box-sizing: border-box;
					 * https://github.com/select2/select2/blob/4.0.1/dist/css/select2.css#L8
					 * Это значит, что указанная выше высота включает в себя border и padding.
					 * https://developer.mozilla.org/en/docs/Web/CSS/box-sizing#Values
					 *
					 * Поэтому мы вынуждены применять именно margin-bottom.
					 */
					if (item.id && 'default' !== item.id && !item.children && p) {
						/** http://stackoverflow.com/a/5744268 */
						result = $('<div/>').css({
							'background-image': 'url(' + data['sprite'] + ')'
							// http://stackoverflow.com/a/7181519
							,'background-position': [
								'-' + p[0] + 'px', '-' + (p[1] + marginBottom) + 'px'
							].join(' ')
							,'background-repeat': 'no-repeat'
							,width : previewWidth
							,height : previewHeight
							,'margin-bottom': marginBottom + 'px'
						});
					}
					return result;
				};
				$element.select2({
					/**
					 * 2015-12-11
					 * Опция dropdownAutoWidth позволяет выпадающей части быть шире,
					 * чем видимая по умолчанию часть выпадающего списка.
						if (this.options.get('dropdownAutoWidth')) {
							css.minWidth = css.width;
							css.width = 'auto';
						}
					 * https://github.com/select2/select2/blob/4.0.1/dist/js/select2.full.js#L4243-L4246
					 * http://stackoverflow.com/a/19321435
					 */
					dropdownAutoWidth: true
					/**
					 * 2015-12-11
					 * https://github.com/select2/select2/blob/4.0.1/dist/js/select2.full.js#L5601
					 * http://select2.github.io/select2/#documentation
					 * 2016-08-10
					 * Это — стиль выпадающего списка,
					 * который иерархически который расположен снаружи основного DOM,
					 * внизу страницы.
					 */
					,dropdownCssClass: 'df-google-font'
					,data: [{id: 'default', text: 'Font Family: Default'}].concat($.map(data['fonts'],
						/**
						 * 2015-11-28
						 * @param {Object[]} variants
						 * @param {String} family
						 * @returns {Object}
						 */
						function(variants, family) {return {
							id: family
							,text: family
							// 2015-11-28
							// http://stackoverflow.com/a/17621060
							,children: $.map(variants,
							   /**
								* 2015-11-28
								* @param {Number[]} datumPoint
								* @param {String} name
								* @returns {Object}
								*/
								function(datumPoint, name) {return {
									// https://developers.google.com/fonts/docs/getting_started#Syntax
									// http://fonts.googleapis.com/css?family=Tangerine:bold
									id: [family, name].join(':')
								   ,text: name
								   //,preview: variant.preview
								   ,alt: family + ' (' + name + ')'
								   ,datumPoint: datumPoint
								   /**
									* 2015-12-13
									* Всплывающая подсказка для элемента.
									* Я назначаю сюда общее значение «Font Family»,
									* чтобы именно эта фраза отображалась
									* при наведении мыши на выпадающий список.
									* Если title не задать, то Select2 по-умолчанию отобразит
									* значение атрибута text:
									* $rendered.prop('title', selection.title || selection.text);
									* https://github.com/select2/select2/blob/4.0.1/dist/js/select2.full.js#L1536
									*/
								   ,'title': $element.attr('title')
								}}
							)
						};}
					)),
					/**
					 * 2015-11-28
					 * http://stackoverflow.com/a/19701390
					 * @name Select2Item
					 * @property {?Select2Item[]} children
					 * https://github.com/select2/select2/blob/4.0.1/dist/js/select2.full.js#L4734-L4750
					 * @property {Boolean} disabled
					 * @property {HTMLOptionElement} element
					 * @property {?String} id		Например: "ABeeZee:regular"
					 * @property {Boolean} selected
					 * @property {String} text	Например: "regular"
					 * @property {?Boolean} loading		Передаётся в templateResult
					 * @property {?Number[]} datumPoint
					 */
					/**
					 * 2015-11-28
					 * https://select2.github.io/announcements-4.0.html#new-matcher
					 *
					 * 1) https://github.com/select2/select2/blob/4.0.1/dist/js/select2.full.js#L3272-L3276
							SelectAdapter.prototype.matches = function (params, data) {
								var matcher = this.options.get('matcher');
								return matcher(params, data);
							};
					 * 2) https://github.com/select2/select2/blob/4.0.1/dist/js/select2.full.js#L4728-L4771
					 *
					 * @param {Object} params
					 * @param {?String} params.term
					 * https://github.com/select2/select2/blob/4.0.1/dist/js/select2.full.js#L4762
					 * @param {Select2Item} item
					 * @param {Select2Item=} parent
					 * @returns {?Select2Item}
					 * https://github.com/select2/select2/blob/4.0.1/dist/js/select2.full.js#L4770
					 */
					matcher: function matcher(params, item, parent) {
						//return $.fn.select2.defaults.defaults.matcher(params, item);
						// Always return the object if there is nothing to compare
						/** @type {?Select2Item} */
						var result;
						if ('' === $.trim(params.term)) {
							result = item;
						}
						else if (!item.children || !item.children.length) {
							var dfContains = function(haystack, needle) {
								return -1 < haystack.toUpperCase().indexOf(needle.toUpperCase());
							};
							result =
								dfContains(item.text, params.term)
								|| (parent && dfContains(parent.text, params.term))
								? item
								: null
							;
						}
						// Do a recursive check for options with children
						else {
							// Clone the data object if there are children
							// This is required as we modify the object to remove any non-matches
							var match = df.clone(item);
							// Check each child of the option
							for (var c = item.children.length - 1; 0 <= c; c--) {
								var child = item.children[c];
								var matches = matcher(params, child, item);
								// If there wasn't a match, remove the object in the array
								if (!matches) {
									match.children.splice(c, 1);
								}
							}
							result = match.children.length ? match : matcher(params, match, parent);
						}
						return result;
					},
					/**
					 * 2015-12-01
					 * https://select2.github.io/examples.html#templating
					 * @param {Select2Item} item
					 * @returns {String}
					 */
					templateResult: function(item) {return preview(item);},
					/**
					 * 2015-11-28
					 * http://stackoverflow.com/a/33971933
					 * @param {Select2Item} item
					 * @returns {String}
					 */
					templateSelection: function(item) {
						/**
						 * 2015-12-10
						 * Опытным путём установил, чтол делить лучше на 12:
						 * при миниатюрах высотой 28 пикселей
						 * хорошо смотрится нижний отступ в 2 пикселя,
						 * а при миниаютрах высотой в 31 пиксель — в 3 пикселя.
						 */
						return preview(item, Math.round(previewHeight / 12));
					}
				});
				if (config.value && config.value.length) {
					// http://stackoverflow.com/a/30477163
					$element.val(config.value).change();
				}
			}
		});
	}
);});