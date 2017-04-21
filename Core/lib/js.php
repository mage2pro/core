<?php
/**
 * 2015-02-17
 * Экранирует строку для вставки её в код на JavaScript.
 * @uses json_encode() рекомендуют
 * как самый правильный способ вставки строки из PHP в JavaScript:
 * http://stackoverflow.com/a/169035
 * Заменяем символ одинарной кавычки его кодом Unicode,
 * чтобы результат метода можно было вставлять внутрь обрамленной одиночными кавычками строки,
 * например:
	var $name = '<?php echo df_ejs($name); ?>';
 * @used-by df_admin_button_location()
 * @used-by Df_Admin_Config_DynamicTable_Column::renderTemplate()
 * @used-by app/design/adminhtml/rm/default/template/df/admin/column/select.phtml
 * @used-by app/design/adminhtml/rm/default/template/df/admin/field/button.phtml
 * @used-by app/design/frontend/rm/default/template/df/checkout/onepage/shipping_method/available/js.phtml
 * @param string $text
 * @return string
 */
function df_ejs($text) {return str_replace("'", '\u0027', df_trim(json_encode($text), '"'));}

/**
 * 2015-10-26 https://mage2.pro/t/145
 * 2016-11-28
 * Пример: https://github.com/magento/magento2/blob/2.1.2/app/code/Magento/Theme/view/frontend/templates/js/cookie.phtml#L16-L26
 * Такой синтаксис, в отличие от @see df_widget(),
 * не позволяет нам иметь в JavaScript объект-элемент DOM вторым параметром:
 * https://github.com/magento/magento2/blob/2.1.2/lib/web/mage/apply/main.js#L69-L70
 * 2017-04-21
 * Эта функция не привязывает код JavaScript браузерного компонента ни к какому элементу HTML.
 * Если Вам нужна такая привязка, то используйте альтернативную функцию @see df_widget()
 *
 * @used-by df_fe_init()
 * @used-by \Df\Checkout\Block\Messages::_toHtml()
 * @used-by \Df\Checkout\Block\Payment::_toHtml()
 * @used-by \Df\Sso\Css::_toHtml()
 * @used-by \Dfe\AmazonLogin\Button::loggedIn()
 * @used-by \Dfe\Customer\Block::_toHtml()
 * @used-by \Dfe\Markdown\FormElement::getAfterElementHtml()
 * @param string|object $m
 * Функция допускает в качестве $m:
 * 1) Имя модуля. Например: «A_B».
 * 2) Имя класса. Например: «A\B\C».
 * 3) Объект. Сводится к случаю 2 посредством @see get_class()
 * @param string $script
 * @param array(string => mixed) $params
 * @return string
 */
function df_js($m, $script, array $params = []) {return df_tag(
	'script', ['type' => 'text/x-magento-init'], df_json_encode([
		'*' => [df_cc_path(df_module_name($m), $script) => $params]
	])
);}

/**
 * 2016-11-28
 * Такой синтаксис, в отличие от @see df_js(),
 * позволяет нам иметь в JavaScript объект-элемент DOM вторым параметром:
 * https://github.com/magento/magento2/blob/2.1.2/lib/web/mage/apply/main.js#L69-L70
 * Пример: https://github.com/magento/magento2/blob/2.1.2/app/code/Magento/Checkout/view/frontend/templates/cart/minicart.phtml#L30-L38
 * @see json_encode всегда использует двойные кавычки,
 * а @see \Df\Core\Format\Html\Tag::openTagWithAttributesAsText()
 * всегда обрамляет значение в одинарные кавычки,
 * поэтому df_widget() всегда совместима с @see df_tag()
 *
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
 *			<?php echo $block->getChildHtml('minicart.addons'); ?>
 *		</div>
 * https://github.com/magento/magento2/blob/2.1.2/app/code/Magento/Checkout/view/frontend/templates/cart/minicart.phtml#L30-L38
 * Таким образом код JavaScript браузерного компонента
 * оказывается ассоциированным с неким конкретным элементом HTML:
 * этим данный способ инициализации отличается от способа функции @see df_js(),
 * которая не привязывает код JavaScript браузерного компонента ни к какому элементу HTML.
 *
 * @used-by \Df\Sso\Button\Js::attributes()
 * @used-by \Dfe\Klarna\Button::_toHtml()
 *
 * @param string|object $m
 * Функция допускает в качестве $m:
 * 1) Имя модуля. Например: «A_B».
 * 2) Имя класса. Например: «A\B\C».
 * 3) Объект. Сводится к случаю 2 посредством @see get_class()
 * @param string $script
 * @param array(string => mixed) $params
 * @return array(string => string)
 */
function df_widget($m, $script, array $params = []) {return [
	'data-mage-init' => df_json_encode([df_cc_path(df_module_name($m), $script) => $params])
];}