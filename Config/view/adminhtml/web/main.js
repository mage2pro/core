require(['jquery', 'domReady!'], function($) {
	if (
		-1 !== location.href.indexOf('system_config/edit/section/dfe_')
		|| -1 !== location.href.indexOf('system_config/edit/section/df_')
	) {
		$('body').addClass('df-config');
	}
	/** @type {jQuery} HTMLFormElement */
	var $form = $('form#config-edit-form');
	/** @type {jQuery} HTMLInputElement[] */
	var $checkboxes = $('input[type=checkbox].df-checkbox', $form);
	/**
	 * 2015-12-27
	 * Тонкий момент.
	 * Стандартный класс FormElementDependenceController не приспособлен к работе с чекбоксами.
	 * Он проверяет значение поля примитивным способом: from.value
	 * https://github.com/magento/magento2/blob/2.0.0/lib/web/mage/adminhtml/form.js#L366
	 * Этот способов для чекбокса не работает:
	 * .value всегда возвращает начальное значение чекбокса (причём в формате «on» / «off»).
	 *
	 * Правильным является, например, такой способ: jQuery(idFrom).is(":checked")
	 * http://stackoverflow.com/a/2204253
	 * Однако менять код класса FormElementDependenceController — это слишком сложновато.
	 * Поэтому я придумал другой способ: динамически менять значение свойства .value.
	 */
	$checkboxes.click(function() {this.value = $(this).is(':checked') ? 1 : 0;});
	/**
	 * 2015-12-27
	 * Ещё один тонкий момент.
	 * По умолчанию .value возвращает значение чекбокса в формате «on» / «off».
	 * Это приводит к тому, что при замете select со значениями «Yes» / «No» на чекбокс
	 * нам приходится в секции «depends» менять «1» на «on» и «0» на «off»:
	 * <depends><field id='enable'>on</field></depends>
	 * https://github.com/magento/magento2/blob/2.0.0/lib/web/mage/adminhtml/form.js#L366
	 * Чтобы этого не делать, вызовом updateCheckbox($checkboxes); делаю так,
	 * чтобы .value возвращало значение в формате «1» / «0».
	 *
	 * Сначала хотел сделать это приличным способом: updateCheckbox($checkboxes);
	 * Однако FormElementDependenceController.initialize() срабатывает раньше нашего метода,
	 * вызывает FormElementDependenceController.trackChange(), и там сравнивает «on» с «1»:
	 * https://github.com/magento/magento2/blob/2.0.0/lib/web/mage/adminhtml/form.js#L334
	 * Поэтому приходится поступать юмористическим способом:
	 * имитируя двойное нажатие на чекбокс.
	 */
	$checkboxes.each(function() {$(this).click().click();});
	$checkboxes.each(function() {
		// 2015-12-21
		// Ядро по клику на подписи к галке уже умеет устанавливать и снимать эту галку.
		// Я так и не разобрался, где конкретно ядро это делает, но делает.
		// Мы же в дополнение к этому устанавливаем правильный курсор — руку,
		// чтобы администратору было понятно, что на надпись можно кликнуть.
		// 2015-12-28
		// Добавил .parent('td'),
		// чтобы данное правило распространялось только на простые чекбоксы
		// и не распространялось на чекбоксы внутри моих филдсетов,
		// потому что для них указанный ниже селектор label относится ко всему филдсету,
		// а руку для чекбоксового label мы устанавливаем другим, более простым способом:
		// http://code.dmitry-fedyuk.com/m2/all/blob/a4d4a657b47b528cd26bbd7d5320b0f56a045b3e/Core/view/base/web/main.less#L18
		// input.df-checkbox ~ label {cursor: pointer;}
		$(this).parent('td').closest('tr').children('td.label').children('label')
			// 2015-12-28
			// Для практически label в ядре стоит padding-top: 7px.
			// Это правильно почти для всех label:
			// ведь элемент управления тоже имеет верхний padding (и border):
			// например, у input это расстояние от верхней кромки до текста.
			// Для label чекбоксов такой верхний padding не нужен,
			// поэтому устанавливаем свой класс,
			// чтобы к нему можно было привязать стили отключения padding.
			// http://code.dmitry-fedyuk.com/m2/all/blob/dd5ad387ad8b27f748d64a8b6224cc0956f98177/Config/view/adminhtml/web/main.less#L10
			.addClass('df-label-checkbox')
			.hover(
				function() {$(this).addClass('df-hover');}
				, function() {$(this).removeClass('df-hover');}
			)
		;
	});
	/**
	 * 2015-12-21
	 * Формы не передают на сервер выключенные чекбоксы.
	 * http://stackoverflow.com/questions/3029870
	 */
	$form.submit(function() {
		var $form = $(this);
		$('input[type=checkbox]:not(:checked)', $form).each(function() {
			$form.append($('<input>').attr({type: 'hidden', name: this.name, value: 0}));
		});
	});
});