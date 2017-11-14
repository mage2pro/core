// 2017-06-14
define([], function() {return function(data) {
	require([
		'jquery', 'ko', 'Df_Intl/dic', 'Df_Intl/t'
		/**
		 * 2017-11-14
		 * "Magento 2.0.x: «Script error for: Magento_Ui/js/lib/knockout/template/renderer»"
		 * https://github.com/mage2pro/core/issues/47
		 */
		,'Magento_Ui/js/lib/' + (data['isMagento2.0.x'] ? 'ko' : 'knockout') + '/template/renderer'
		,'module'
	], function($, ko, dic, $t, renderer, module) {
		dic.set(data['dic']);
		var locations = {
			'a': 'Link label',
			'abbr': 'Abbreviated phrase',
			'acronym': 'An acronym',
			'address': 'Contact information',
			'b': 'Bold text',
			'big': 'Bigger text',
			'blockquote': 'Long quotation',
			'button': 'Push button',
			'caption': 'Table caption',
			'center': 'Centered text',
			'cite': 'Citation',
			'del': 'Deleted text',
			'dfn': 'Term',
			'em': 'Emphasized text',
			'h1': 'Heading level 1',
			'h2': 'Heading level 2',
			'h3': 'Heading level 3',
			'h4': 'Heading level 4',
			'h5': 'Heading level 5',
			'h6': 'Heading level 6',
			'i': 'Italic text',
			'img': 'Image',
			'input': 'Form element',
			'ins': 'Inserted text',
			'label': 'Label for an input element.',
			'legend': 'Caption for the fieldset element',
			'q': 'Short quotation',
			'select': 'List options',
			'small': 'Smaller text',
			'span': 'Span element',
			'strike': 'Strikethrough text',
			'strong': 'Strong emphasized text',
			'sub': 'Subscript text',
			'sup': 'Superscript text',
			'u': 'Underlined text',
			'var': 'Variable part of a text'
		};
		/**
		 * 2017-06-14
		 * @param {Boolean} isUpdate
		 */
		function p(isUpdate) {return (
			/**
			 * 2017-06-14
			 * @param {HTMLElement} e
			 * @param {Function} fValue
			 */
			function(e, fValue) {
				// 2017-06-14 «8» means a comment node like <!-- ko df_i18n: 'Credit Card Number'-->
				/** @type {jQuery} HTMLElement */
				var $e = 8 !== e.nodeType ? $(e) : (isUpdate ? $(e).next('span') : $('<span/>').insertAfter(e));
				/** @type {String} */
				var original = ko.unwrap(fValue() || '');
				/** @type {String} */
				var translated = $t(original);
				if ((module.config() || {}).inlineTranslation) {
					$e.attr('data-translate', JSON.stringify([{
						'location': locations[$e.prop('tagName').toLowerCase()] || 'Text'
						,'original': original
						,'shown': translated
						,'translated': translated
					}]));
				}
				$e.text(translated);
			}
		);}
		/** @type {String} */
		var id = 'df_i18n';
		ko.bindingHandlers[id] = {init: p(false), update: p(true)};
		ko.virtualElements.allowedBindings[id] = true;
		/**
		 * 2017-11-14
		 * Magento 2.0.x: «renderer.addAttribute is not a function»
		 * https://github.com/mage2pro/core/issues/48
		 */
		if (!data['isMagento2.0.x']) {
			/**
			 * 2017-06-13
			 * I do not use it yet, and made it by analogy with the «i18n» binding:
			 * https://github.com/magento/magento2/blob/2.1.7/app/code/Magento/Ui/view/base/web/js/lib/knockout/bindings/i18n.js#L174-L180
			 * @uses Magento_Ui/js/lib/knockout/template/renderer::addAttribute()
			 * https://github.com/magento/magento2/blob/2.1.7/app/code/Magento/Ui/view/base/web/js/lib/knockout/template/renderer.js#L94-L118
			 * @uses Magento_Ui/js/lib/knockout/template/renderer::addNode()
			 * https://github.com/magento/magento2/blob/2.1.7/app/code/Magento/Ui/view/base/web/js/lib/knockout/template/renderer.js#L132-L156
			 */
			renderer.addAttribute('df-translate', {binding: id});
			renderer.addNode('df-translate', {binding: id});
		}
	});
}});