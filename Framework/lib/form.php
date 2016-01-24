<?php
use Df\Framework\Data\Form\Element as E;
use Magento\Framework\Data\Form\Element\AbstractElement as AE;
define('DF_FA', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.css');
/**
 * 2015-11-28
 * @param AE|E $e
 * @param string $class [optional]
 * @param string|string[] $css [optional]
 * @param array(string => string) $params [optional]
 * @param string|null $path [optional]
 * @return void
 */
function df_fe_init(AE $e, $class = '', $css = [], $params = [], $path = null) {
	$class = $class ?: get_class($e);
	/** @var string $moduleName */
	$moduleName = df_module_name($class);
	// 2015-12-29
	// Мы различаем ситуации, когда $path равно null и пустой строке.
	// *) null означает, что имя ресурса должно определяться по имени класса.
	// *) пустая строка означает, что ресурс не имеет префикса, т.е. его имя просто «main».
	if (is_null($path)) {
		/** @var string[] $classA */
		$classA = df_explode_class_lc($class);
		$classLast = array_pop($classA);
		switch ($classLast) {
			// Если имя класса заканчивается на FormElement,
			// то это окончание в пути к ресурсу отбрасываем.
			case 'formElement':
				// $path будет равно null
				break;
			// Если имя класса заканчивается на Element,
			// то в качестве пути к ресурсу используем предыдущую часть класса.
			// Пример: «Dfe\SalesSequence\Config\Matrix\Element» => «matrix»
			case 'element':
				$path = array_pop($classA);
				break;
			default:
				$path = $classLast;
		}
	}
	// 2015-12-29
	// Используем df_cc_clean, чтобы отбросить $path, равный пустой строке.
	// Если имя класса заканчивается на FormElement, то это окончание в пути к ресурсу отбрасываем.
	$path = df_cc_clean('/', 'formElement', $path, 'main');
	/**
	 * 2015-12-29
	 * На практике заметил, что основной файл CSS используется почти всегда,
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
	/**
	 * 2015-12-30
	 * Раньше я думал, что основной файл CSS используется всегда, однако нашлось исключение:
	 * @see \Dfe\CurrencyFormat\FormElement обходится в настоящее время без CSS.
	 */
	if (df_asset_exists($path, $moduleName, 'less')) {
		$css[]= df_asset_name($path, $moduleName, 'css');
	}
	$e['before_element_html'] .= df_cc_n(
		!df_asset_exists($path, $moduleName, 'js') ? null : df_x_magento_init(
			df_cc_url($moduleName, $path)
			, ['id' => $e->getHtmlId()] + $params
		)
		,df_link_inline($css)
	);
}

/**
 * 2016-01-06
 * @param AE $e
 * @param string $suffix [optional]
 * @return array(string => string)
 */
function df_fe_uid(AE $e, $suffix = null) {return ['data-ui-id' => E::uidSt($e, $suffix)];}

/**
 * 2016-01-08
 * @param AE $e
 * @param string $uidSuffix [optional]
 * @return array(string => string)
 */
function df_fe_attrs(AE $e, $uidSuffix = null) {
	return
		['id' => $e->getHtmlId(), 'name' => $e->getName()]
		+ df_fe_uid($e, $uidSuffix)
		+ df_select_a($e->getData(), $e->getHtmlAttributes())
	;
}


/**
 * 2015-12-14
 * @param AE|E $e
 * @return void
 */
function df_hide(AE $e) {$e->setContainerClass('df-hidden');}


