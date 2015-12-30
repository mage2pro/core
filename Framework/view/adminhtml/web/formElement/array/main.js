// 2015-12-28
define(['jquery', 'domReady!'], function($) {return (
	/**
	 * @param {Object} config
	 * @param {String} config.id
	 */
	function(config) {
		/** @type {jQuery} HTMLFieldSetElement */
		var $element = $(document.getElementById(config.id));
		/** @type {jQuery} HTMLFieldSetElement */
		var $template = $element.children('.df-name-template');
		var $delete = $('<div>').addClass('df-delete fa fa-trash-o').click(function() {
			$(this).parent().remove();
		});
		$element.children('.df-field').prepend($delete);
		(function() {
			var $toolbar = $('<div/>').addClass('toolbar');
			$element.after($toolbar);
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
				/** @type {jQuery} HTMLFieldSetElement */
				var $item = $template.clone(true).removeClass('df-hidden');
				/**
				 * 2015-12-30
				 * Нумерация элементов начинается с нуля:
				 * http://code.dmitry-fedyuk.com/m2/all/blob/3d99a5fa88177f8aca5d69f6f9b4ca4865735b9f/Framework/Data/Form/Element/ArrayT.php#L22
				 * При этом один из элементов является шаблоном (невидима),
				 * поэтому порядковый номер нового элемента
				 * равен количеству уже имеющихся элементов минус один
				 * или же (что то же самое) количеству братьев элемента-шаблона.
				 *
				 *
				 * Отныне, с появлением функциональности удаления элементов,
				 * мы не можем использовать наивный алгоритм $template.siblings('.df-field').length.
				 */
				var $items = $template.siblings('.df-field');
				// 2015-12-30
				// Случай с отсутствием элементов приходится обрабатывать отдельно,
				// потому что Math.max.apply(null, []) возвращает -Infinity,
				// и 1 + Math.max.apply(null, []) всё равно возвращает -Infinity.
				/** @type {Number} */
				var ordering = 0 === $items.length ? 0 : 1 + Math.max.apply(null, $items.map(function() {
					var prefix = 'df-name-';
					// 2015-12-30
					// http://stackoverflow.com/a/1227309
					return parseInt(this.className.split(/\s+/).find(function(className) {
						return 0 === className.indexOf(prefix);
					}).replace(prefix, ''));
				}).get());
				$('link', $item).remove();
				/**
				 * 2015-12-30
				 * «As of jQuery 1.6, the .attr() method returns undefined
				 * for attributes that have not been set.»
				 * http://api.jquery.com/attr/#entry-longdesc
				 * @param {jQuery} $element HTMLElement
				 * @param {String} attrName
				 * @param {String} from
				 * @param {String} to
				 */
				var replaceAttr = function($element, attrName, from, to) {
					/** @type {?String} */
					var value = $element.attr(attrName);
					if ('undefined' !== typeof value) {
						$element.attr(attrName, value.replace(from, to));
					}
				};
				$('*', $item).add($item).each(function() {
					var $this = $(this);
					replaceAttr($this, 'class', 'df-name-template', 'df-name-' + ordering);
					$.each(['id', 'data-ui-id', 'name'], function() {
						replaceAttr($this, this, '[template]', '[' + ordering + ']');
					});
				});
				$element.append($item);
				/**
				 * 2015-12-30
				 * «Ядро по клику на подписи к галке уже умеет устанавливать и снимать эту галку.
				 * Я так и не разобрался, где конкретно ядро это делает, но делает.»
				 * http://code.dmitry-fedyuk.com/m2/all/blob/814bdb18364d1146ec4edd6684bd89bada1f6488/Config/view/adminhtml/web/main.js#L54
				 * Однако эта функциональность почему-то утрачивается
				 * при клонировании нашего шаблона.
				 * Поэтому добавляем её вручную.
				 */
				$('input[type=checkbox].df-checkbox', $item).each(function() {
					/** @type {HTMLInputElement} */
					var checkbox = this;
					/** @type {jQuery} HTMLInputElement */
					var $checkbox = $(this);
					// 2015-12-30
					// Скопировал отсюда: http://code.dmitry-fedyuk.com/m2/all/blob/814bdb18364d1146ec4edd6684bd89bada1f6488/Config/view/adminhtml/web/main.js#L29
					$checkbox.siblings('label').click(function() {
						// 2015-12-30
						// *) Почему-то приходится устанавливать и .prop, и .value.
						// *) Среда разработки ругается,
						// если устанавливать целые числа, а не строки.
						checkbox.value = $checkbox.is(':checked') ? '0' : '1';
						$checkbox.prop('checked', checkbox.value);
					});
				});
			});
		})();
		/**
		 * 2015-12-30
		 * По аналогии с http://code.dmitry-fedyuk.com/m2e/markdown/blob/d030a44bfe75765d54d68acf106e2fbb9bd66b4c/view/adminhtml/web/main.js#L364
		 *
		 * По-правильному надо обрабатывать не beforeSubmit, а submit,
		 * потому что при beforeSubmit мы ещё не знаем, будет ли форма отправлена на сервер:
		 * вдруг валидаторы запретят отправку на сервер.
		 * Поэтому перед удалением шаблона нам важно быть уверенным,
		 * что форма действительно отправляется на сервер.
		 * Однако система почему-то игнорирует удаление полей формы на submit,
		 * поэтому прицепился пока на beforeSubmit.
		 */
		var $form = $element.closest('form');
		$form.bind('beforeSubmit', function() {
			/**
			 * 2015-12-30
			 * Сервер так устроен, что если для конкретного поля формы
			 * не придут данные с сервер (а при отсутствии элементов они не придут),
			 * то сервер не обновляет значение в базе данных для этого поля.
			 * Это приводит к тому эффекту, что если удалить все элементы, то сервер не сохранит данные.
			 * Чтобы этого избежать, при отсутствии элементов передаём на сервер фейковый.
			 */
			if (0 === $template.siblings('.df-field').length) {
				var fakeName = $(':input', $template).first().attr('name').replace(
					'[template]', '[fake]'
				);
				// 2015-12-30
				// beforeSubmit не всегда приводит к отправке данных на сервер
				// (валидатор может запретить отправку),
				// поэтому фейковый элемент может узже существовать.
				if (0 === $('[name="' + fakeName + '"]', $form).length) {
					$form.append($('<input>').attr({
						name: fakeName, type: 'hidden', value: ''
					}))
				}
			}
			$template.remove();
		});
	}
);});