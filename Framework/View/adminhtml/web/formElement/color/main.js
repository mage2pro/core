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
			// 2015-12-17
			// https://bgrins.github.io/spectrum/#options-containerClassName
			,containerClassName: ''
			// 2015-12-17
			// Скрывает выпадающую панель при клике на какой-либо цвет палитры.
			// https://bgrins.github.io/spectrum/#options-hideAfterPaletteSelect
			,hideAfterPaletteSelect: true
			// 2015-12-17
			// https://bgrins.github.io/spectrum/#options-showSelectionPalette
			// Ключ для хранения в Local Storage ранее выбранных цветов
			// (должна быть включена опция showSelectionPalette).
			,localStorageKey: 'mage2.pro'
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
			// например: rgba(0, 0, 255, 0.78).
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
			// Запоминает выбранные ранее цвета.
			// Надо настроить ещё localStorageKey.
			// https://bgrins.github.io/spectrum/#options-showSelectionPalette
			,showSelectionPalette: true
			,show: function(color) {
			}
			,className: 'full-spectrum'

			,maxPaletteSize: 10
			, palette: [
				['rgb(0, 0, 0)', 'rgb(64, 64, 64)', 'rgb(67, 67, 67)', 'rgb(102, 102, 102)', /*'rgb(153, 153, 153)','rgb(183, 183, 183)',*/
				'rgb(204, 204, 204)', 'rgb(217, 217, 217)', /*'rgb(239, 239, 239)', 'rgb(243, 243, 243)',*/ 'rgb(255, 255, 255)'],
				['rgb(152, 0, 0)', 'rgb(255, 0, 0)', 'rgb(255, 153, 0)', 'rgb(255, 255, 0)', 'rgb(0, 255, 0)',
				'rgb(0, 255, 255)', 'rgb(74, 134, 232)', 'rgb(0, 0, 255)', 'rgb(153, 0, 255)', 'rgb(255, 0, 255)'],
				['rgb(230, 184, 175)', 'rgb(244, 204, 204)', 'rgb(252, 229, 205)', 'rgb(255, 242, 204)', 'rgb(217, 234, 211)',
				'rgb(208, 224, 227)', 'rgb(201, 218, 248)', 'rgb(207, 226, 243)', 'rgb(217, 210, 233)', 'rgb(234, 209, 220)',
				'rgb(221, 126, 107)', 'rgb(234, 153, 153)', 'rgb(249, 203, 156)', 'rgb(255, 229, 153)', 'rgb(182, 215, 168)',
				'rgb(162, 196, 201)', 'rgb(164, 194, 244)', 'rgb(159, 197, 232)', 'rgb(180, 167, 214)', 'rgb(213, 166, 189)',
				'rgb(204, 65, 37)', 'rgb(224, 102, 102)', 'rgb(246, 178, 107)', 'rgb(255, 217, 102)', 'rgb(147, 196, 125)',
				'rgb(118, 165, 175)', 'rgb(109, 158, 235)', 'rgb(111, 168, 220)', 'rgb(142, 124, 195)', 'rgb(194, 123, 160)',
				'rgb(166, 28, 0)', 'rgb(204, 0, 0)', 'rgb(230, 145, 56)', 'rgb(241, 194, 50)', 'rgb(106, 168, 79)',
				'rgb(69, 129, 142)', 'rgb(60, 120, 216)', 'rgb(61, 133, 198)', 'rgb(103, 78, 167)', 'rgb(166, 77, 121)',
				/*'rgb(133, 32, 12)', 'rgb(153, 0, 0)', 'rgb(180, 95, 6)', 'rgb(191, 144, 0)', 'rgb(56, 118, 29)',
				'rgb(19, 79, 92)', 'rgb(17, 85, 204)', 'rgb(11, 83, 148)', 'rgb(53, 28, 117)', 'rgb(116, 27, 71)',*/
				'rgb(91, 15, 0)', 'rgb(102, 0, 0)', 'rgb(120, 63, 4)', 'rgb(127, 96, 0)', 'rgb(39, 78, 19)',
				'rgb(12, 52, 61)', 'rgb(28, 69, 135)', 'rgb(7, 55, 99)', 'rgb(32, 18, 77)', 'rgb(76, 17, 48)']
			]
		});
		$color.addClass('df-hidden');
	}
);});