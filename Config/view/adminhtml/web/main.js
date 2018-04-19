/**
 * 2017-07-18
 * I have tried to load it via df_js():
 * https://github.com/mage2pro/core/blob/2.8.22/Config/Js.php#L6-L15
 * https://github.com/mage2pro/core/blob/2.8.22/Config/view/adminhtml/web/main.js#L1
 * https://github.com/mage2pro/core/blob/2.8.22/Config/view/adminhtml/layout/adminhtml_system_config_edit.xml#L8-L12
 * It worked perfectly on my local PC (the latest Magento 2.2 `develop` branch)
 * But it caused a strange error on the seller-services.com website (Magento 2.1.7):
 * `Unable to uncheck the «Test Mode» checkbox`: https://mage2.pro/t/4188
 * I have spend 3 hours to debug it, but without any success :-(
 * It looks like the checkbox handling code below did not work on seller-services.com:
 * https://github.com/mage2pro/core/blob/2.8.22/Config/view/adminhtml/web/main.js#L29-L129
 * See also:
 * Note 1.`The fields with type='checkbox' are not saved in the backend «Stores» → «Configuration» section`:
 * https://mage2.pro/t/333
 * Note 2. `How is the «Save Config» backend button implemented?` https://mage2.pro/t/4189
 */
require(['jquery', 'domReady!'], function($) {
	/**
	 * 2017-07-18
	 * It should be loaded is a separate `require` block.
	 * I have tried it to load in the `require` block above,
	 * and it caused the Df_Intl/dic::set() was called before Df_Intl/dic::get(), and it broke everything.
	 * https://github.com/mage2pro/core/blob/2.8.22/Intl/view/base/web/dic.js#L3-L15
	 */
	require(['Df_Ui/validator/popular']);
	/**
	 * 2016-06-27
	 * Раньше мы следующим (неверным) образом определяли,
	 * находится ли администратор в разделе настроек одного из модулей Mage2.PRO
	 *	if (
	 *		-1 !== location.href.indexOf('system_config/edit/section/dfe_')
	 *		|| -1 !== location.href.indexOf('system_config/edit/section/df_')
	 *	) {
	 *		// находимся
	 *	}
	 * Этот способ — хороший, почти идеальный, но... всё-таки неверный,
	 * потому что один из разделов настроек одного из модулей Mage2.PRO
	 * является для администратора магазина разделом настроек по-умолчанию
	 * (я не делал этого специально, но — нак уж исторически сложилось),
	 * и вот для этого разделе веб-адрес не будет содержать части
	 * «system_config/edit/section/df», а будет иметь вид:
	 * /префикс администратичной части/admin/system_config/index/ключ, если включен
	 * Поэтому нам потребовался иной способ установка факта нахождения адмнистратора
	 * в разделе настроек одного из модулей Mage2.PRO.
	 */
	/** @type {jQuery} HTMLFormElement */
	var $form = $('form#config-edit-form');
	/** @type {String} */
	var action = $form.attr('action');
	if (
		-1 !== action.indexOf('/section/df_')
		|| -1 !== action.indexOf('/section/dfe_')
		|| -1 !== action.indexOf('/section/doormall_') // 2018-04-19
	) {
		$('body').addClass('df-config');
	}
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
	$checkboxes.each(function() {
		/**
		 * 2016-01-29
		 * Когда область действия настроек — не глобальная,
		 * то чекбокс «Enable?» изначально находится в состоянии disabled
		 * и переводится в состояние enabled снятием соседней галки «Use Default».
		 * Так вот, .click() для чекбокса в состоянии disabled просто игнорируется,
		 * поэтому временно переводим чекбокс в состояние enabled.
		 * http://stackoverflow.com/questions/1414365
		 */
		var disabled = this.disabled;
		this.disabled = false;
		$(this).click().click();
		this.disabled = disabled;
	});
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
		// https://github.com/mage2pro/core/tree/a4d4a657b47b528cd26bbd7d5320b0f56a045b3e/Core/view/base/web/main.less#L18
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
			// https://github.com/mage2pro/core/tree/dd5ad387ad8b27f748d64a8b6224cc0956f98177/Config/view/adminhtml/web/main.less#L10
			.addClass('df-label-checkbox')
			.hover(
				function() {$(this).addClass('df-hover');}
				,function() {$(this).removeClass('df-hover');}
			)
		;
	});
	// 2015-12-21 Формы не передают на сервер выключенные чекбоксы. http://stackoverflow.com/questions/3029870
	// 2017-07-18
	// Note 1.
	// `The fields with type='checkbox' are not saved in the backend «Stores» → «Configuration» section`:
	// https://mage2.pro/t/333
	// Note 2. `How is the «Save Config» backend button implemented?` https://mage2.pro/t/4189
	$form.submit(function() {
		var $form = $(this);
		/**
		 * 2016-01-01
		 * Добавил очень важный селектор «:visible».
		 * Разработанный ранее алгоритм после снятия галки «Enable?» и сохранении формы
		 * приводил к уничтожению всех (скрытых после снятия галки «Enable?») данных,
		 * так что после повторной установки галки «Enable?» все данные приходилось вводить заново.
		 * Исправляем это: не создаём фейковое поле, если наш чекбокс скрыт снятием галки «Enable?».
		 * Смотрите также: https://github.com/mage2pro/core/tree/e8b94162/Framework/view/adminhtml/web/formElement/array/main.js#L153
		 */
		$('input[type=checkbox]:visible:not(:checked)', $form).each(function() {
			$form.append($('<input>').attr({type: 'hidden', name: this.name, value: 0}));
		});
		/**
		 * 2017-10-15
		 * It is for the disabled dropdowns.
		 * @see Df_Framework/formElement/select2/main.js
		 * For not it is used by the Stripe module for the «Payment Currency» dropdown:
		 * it is disabled (and has the preselected value) for Brazil andf Mexico:
		 * «Brazilian Stripe accounts (currently in Preview) can only charge in Brazilian Real»:
		 * https://github.com/mage2pro/stripe/issues/31
		 * «Mexican Stripe accounts (currently in Preview) can only charge in Mexican Peso»
		 * https://github.com/mage2pro/stripe/issues/32
		 */
		$(':input.df-disabled', $form).each(function() {
			$form.append($('<input>').attr({type: 'hidden', name: this.name, value: $(this).val()}));
		});
	});
});