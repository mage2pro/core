<?php
use Df\Framework\Data\Form\Element;
use Magento\Framework\Data\Form\Element\AbstractElement;
define('DF_FA', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.css');
/**
 * 2015-11-28
 * @param AbstractElement $element
 * @param string $class [optional]
 * @param string|string[] $css [optional]
 * @param array(string => string) $params [optional]
 * @param string|null $path [optional]
 * @return void
 */
function df_form_element_init(AbstractElement $element, $class = '', $css = [], $params = [], $path = null) {
	$class = $class ?: get_class($element);
	/** @var string $moduleName */
	$moduleName = df_module_name($class);
	// 2015-12-29
	// Мы различаем ситуации, когда $path равно null и пустой строке.
	// *) null означает, что имя ресурса должно определяться по имени класса.
	// *) пустая строка означает, что ресурс не имеет префикса, т.е. его имя просто «main».
	$path = !is_null($path) ? $path : df_class_last_lc($class);
	// 2015-12-29
	// Используем df_cc_clean, чтобы отбросить $path, равный пустой строке.
	// Если имя класса заканчивается на FormElement, то это окончание в пути к ресурсу отбрасываем.
	$path = df_cc_clean('/', 'formElement', 'formElement' === $path ? null : $path, 'main');
	/**
	 * 2015-12-29
	 * На практике заметил, что основной файл CSS используется всегда,
	 * и его имя имеет формат: Df_Framework::formElement/color/main.css.
	 * Добавляем его обязательно в конец массива,
	 * чтобы правила основного файла CSS элемента
	 * имели приоритет над правилами библиотечных файлов CSS,
	 * которые элемент мог включать в массив $css.
	 * Обратите внимание, что мы даже не проверяем,
	 * присутствует ли уже $mainCss в массиве $css,
	 * потому что @uses df_link_inline делает это сама.
	 */
	$css = df_array($css);
	$css[]= df_asset_name($path, $moduleName, 'css');
	$element['before_element_html'] .= df_cc_n(
		!df_asset_exists($path, $moduleName, 'js') ? null : df_x_magento_init(
			df_cc_url($moduleName, $path), ['id' => $element->getHtmlId()] + $params
		)
		,df_link_inline($css)
	);
}

/**
 * 2015-12-14
 * @param AbstractElement|Element $element
 * @return void
 */
function df_hide(AbstractElement $element) {$element->setContainerClass('df-hidden');}

