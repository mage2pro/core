define(['jquery', 'Df_Core/ColorPicker', 'domReady!'], function($) {return (
	/**
	 * 2015-11-24
	 * @param {Object} config
	 * @param {String} config.id
	 */
	function(config) {
		/** https://github.com/bgrins/spectrum */
		var $color = $(document.getElementById(config.id));
		$color.spectrum({
			// 2015-12-17
			// Позвляет выбор состояния «цвет отсутствует».
			// Например, пользователь задал нестандартный цвет шрифта,
			// а потом хочет вернуться к стандартному.
			// https://bgrins.github.io/spectrum/#options-color
			allowEmpty: true
			// 2015-12-17
			// Позволяет нам сделать надпись «Cancel» локализуемой.
			// https://mage2.pro/t/79
			// https://bgrins.github.io/spectrum/#options-buttonText
			,cancelText: $.mage.__('Cancel')
			// 2015-12-17
			// Позволяет нам сделать надпись «Choose» локализуемой.
			// https://mage2.pro/t/79
			// https://bgrins.github.io/spectrum/#options-buttonText
			,chooseText: $.mage.__('Choose')
			,clearText: $.mage.__('Clear Color Selection')
			// 2015-12-17
			// Класс CSS выпадающей палитры.
			// https://bgrins.github.io/spectrum/#options-containerClassName
			// Мы его реально используем: https://github.com/mage2pro/core/tree/1a56de90bd5124a1bcaa74c1b1deb110a0647323/Framework/View/adminhtml/web/formElement/color/main.less#L37
			// Ранее мы использовали опцию «className», однако она устарела:
			// https://github.com/bgrins/spectrum/blob/1.7.1/spectrum.js#L53
			,containerClassName: 'df-palette'
			// 2015-12-17
			// Скрывает выпадающую панель при клике на какой-либо цвет палитры.
			// https://bgrins.github.io/spectrum/#options-hideAfterPaletteSelect
			// Эта опция по-умочанию отключена.
			// Я сначала включал её, но потом пришёл к мнению, что в моём случае её лучше отключить.
			// Включение этой опции удобно, когда цвет надо выбирать быстро,
			// а отключение удобно, когда цвет надо выбирать тщательно:
			// после клика по палитре в левой части выпадающей панели
			// мы можем ещё подкорректировать кликнутый цвет в правой части выпадащей палитры.
			,hideAfterPaletteSelect: false
			// 2015-12-17
			// https://bgrins.github.io/spectrum/#options-showSelectionPalette
			// Ключ для хранения в Local Storage ранее выбранных цветов
			// (должна быть включена опция showSelectionPalette).
			,localStorageKey: 'mage2.pro'
			// 2015-12-17
			// Количество сохраняемых в Local Storage ранее выбиравшихся цветов.
			// https://bgrins.github.io/spectrum/#options-maxSelectionSize
			,maxSelectionSize: 10
			,noColorSelectedText: $.mage.__('No Color Selected')
			// 2015-12-17
			// Палитра левой части выпадающей панели.
			// Я разместил в этой палитре цвета темы Luma:
			// https://github.com/magento/magento2/blob/2.0.0/lib/web/css/source/lib/variables/_colors.less
			// Обратите внимание, что массив цветов можно делать двумерным:
			// это позволяет ручную распределить цвета по рядам палитры.
			// Но я этим в данном случае не пользуюсь.
			//
			,palette: [
				"#fff", "#000", "#303030", "#333", "#575757", "#666", "#858585", "#8c8c8c"
				, "#8f8f8f", "#999", "#9e9e9e", "#a3a3a3", "#adadad", "#c2c2c2", "#c7c7c7"
				, "#c9c9c9", "#ccc", "#d1d1d1", "#e3e3e3", "#e5e5e5", "#e8e8e8", "#ebebeb"
				, "#f0f0f0", "#f2f2f2", "#f5f5f5", "#efefef", "#f8f8f8", "#f6f6f6", "#f4f4f4"
				, "#e5efe5", "#bbb", "#aeaeae", "#cecece", "#c1c1c1", "#c5c5c5", "#e4e4e4"
				, "#c6c6c6", "#7e807e", "#eee", "#e2e2e2", "#cdcdcd", "#555", "#494949"
				, "#ff0101", "#e02b27", "#b30000", "#d10029", "#ff5501", "#ff5601", "#ff5700"
				, "#fc5e10", "#006400", "#1979c3", "#006bb4", "#68a8e0", "#fae5e5", "#800080"
				, "#6f4400", "#c07600", "#fdf0d5", "#ffee9c", "#d6ca8e"
			]
			// 2015-12-17
			// Формат отображения цветовых кодов
			// https://bgrins.github.io/spectrum/#options-preferredFormat
			// Что интересно, если указать формат «hex» (как у меня было раньше),
			// то информация о прозрачности (альфа-канале) утратися после выбора значения.
			// Видимо, это дефект Spectrum.
			// Но моим пользователям (администраторам магазинов) формат RGB в любом случае удобней.
			// Обратите внимание, что при вклюсенной опции «showAlpha» (а мы её включаем)
			// администратор имеет возможность задавать степень прозрачности цвета,
			// и тогда фактическим форматом значения становится rgba, а не rgb,
			// например: «rgba(0, 0, 255, 0.78)».
			,preferredFormat: 'rgb'
			// 2015-12-17
			// Включает учёт прозрачности (альфа-канала).
			// https://bgrins.github.io/spectrum/#options-showAlpha
			,showAlpha: true
			// 2015-12-17
			// При открытии палитры делает текущим ранее выбранный цвет.
			// https://bgrins.github.io/spectrum/#options-showInitial
			,showInitial: true
			// 2015-12-17
			// Позволяет врeчную указывать числовой код цвета в выпадающей панели.
			// https://bgrins.github.io/spectrum/#options-showInput
			,showInput: true
			// 2015-12-17
			// Предоставляет выбор цвета из палитры.
			// https://bgrins.github.io/spectrum/#options-showPalette
			// Для нас это имеет большой смысл,
			// потому что администратору удобно выбирать цвет из уже готовых цветов оформительской темы.
			// Более того, мы можем автоматически парсить файл
			// https://github.com/magento/magento2/blob/2.0.0/lib/web/css/source/lib/variables/_colors.less
			// для сбора цветов.
			,showPalette: true
			// 2015-12-17
			// Скрывать ли правую часть выпадающей панели
			// (там расположены элементы управления ручной, детальной настройки цвета).
			// https://bgrins.github.io/spectrum/#options-showPaletteOnly
			// Если включена опция «togglePaletteOnly»,
			// то опция «showPaletteOnly» лишь первичное состояние показа / скрытия правой части,
			// и администратор может скрыть или показать правую часть по своему желанию.
			//
			// Состояние показа / скрытия правой панели сохраняется в Local Storage.
			,showPaletteOnly: true
			// 2015-12-17
			// Запоминает выбранные ранее цвета.
			// Надо настроить ещё localStorageKey.
			// https://bgrins.github.io/spectrum/#options-showSelectionPalette
			,showSelectionPalette: true
			,togglePaletteLessText: $.mage.__('Less')
			,togglePaletteMoreText: $.mage.__('More')
			// 2015-12-17
			// Когда эта опция включена, то внизу левой части выпадающей палитры
			// отображается переключатель «More» / «Less»
			// для показа / скрытия правой части (ручной, детальной настройки цвета).
			// https://bgrins.github.io/spectrum/#options-togglePaletteOnly
			// При этом исходное значение переключателя «More» / «Less»
			// задаётся опцией «showPaletteOnly».
			//
			// Состояние показа / скрытия правой панели сохраняется в Local Storage.
			,togglePaletteOnly: true
		});
		$color.addClass('df-hidden');
	}
);});