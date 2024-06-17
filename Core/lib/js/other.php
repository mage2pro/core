<?php
/**
 * 2016-11-28
 * Такой синтаксис, в отличие от @see df_js(),
 * позволяет нам иметь в JavaScript объект-элемент DOM вторым параметром:
 * https://github.com/magento/magento2/blob/2.1.2/lib/web/mage/apply/main.js#L69-L70
 * Пример: https://github.com/magento/magento2/blob/2.1.2/app/code/Magento/Checkout/view/frontend/templates/cart/minicart.phtml#L30-L38
 * @see json_encode всегда использует двойные кавычки,
 * а @see \Df\Core\Html\Tag::openTagWithAttributesAsText()
 * всегда обрамляет значение в одинарные кавычки,
 * поэтому df_widget() всегда совместима с @see df_tag()
 * 2017-04-21
 * Эта функция предоставляет альтернативный @see df_js() способ
 * инициализации браузерного компонента: параметры инициализации передаются компоненту
 * в значении атрибута «data-mage-init» произвольного тега HTML, например:
 *		<div class="block block-minicart empty"
 *			data-role="dropdownDialog"
 *			data-mage-init='{"dropdownDialog":{
 *				"appendTo":"[data-block=minicart]",
 *				"triggerTarget":".showcart",
 *				"timeout": "2000",
 *				"closeOnMouseLeave": false,
 *				"closeOnEscape": true,
 *				"triggerClass":"active",
 *				"parentClass":"active",
 *				"buttons":[]
 * 			}}'
 * 		>
 *			<div id="minicart-content-wrapper" data-bind="scope: 'minicart_content'">
 *				<!-- ko template: getTemplate() --><!-- /ko -->
 *			</div>
 *			<?= $block->getChildHtml('minicart.addons'); ?>
 *		</div>
 * https://github.com/magento/magento2/blob/2.1.2/app/code/Magento/Checkout/view/frontend/templates/cart/minicart.phtml#L30-L38
 * Таким образом код JavaScript браузерного компонента
 * оказывается ассоциированным с неким конкретным элементом HTML:
 * этим данный способ инициализации отличается от способа функции @see df_js(),
 * которая не привязывает код JavaScript браузерного компонента ни к какому элементу HTML.
 * @used-by \Df\Sso\Button\Js::attributes()
 * @used-by \Dfe\Klarna\Button::_toHtml()
 * @used-by \Dfe\Stripe\Block\Multishipping::_toHtml()
 * @used-by \KingPalm\B2B\Block\Registration::_toHtml()
 * @param string|object|null $m
 * $m could be:
 * 1) A module name: «A_B»
 * 2) A class name: «A\B\C».
 * 3) An object: it comes down to the case 2 via @see get_class()
 * 4) `null`: it comes down to the case 1 with the «Df_Core» module name.
 * @param string|null $s [optional]
 * @param array(string => mixed) $p [optional]
 * @return array(string => string)
 */
function df_widget($m, $s = null, array $p = []):array {return ['data-mage-init' => df_json_encode([
	/**
	 * 2019-11-13
	 * I intentionally use `!is_null($s)` instead of `$s ?:`.
	 * 1) `is_null($s)` means that $s should be `main`.
	 * it is @used-by \KingPalm\B2B\Block\Registration::_toHtml():
	 * https://github.com/kingpalm-com/b2b/blob/1.6.1/Block/Registration.php#L43
	 * 2) `'' === $s` means that $s should not be added to the result at all.
	 */
	df_cc_path(df_module_name($m), !is_null($s) ? $s : 'main') => $p
])];}