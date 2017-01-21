<?php
use Df\Framework\Form\Element as E;
use Df\Framework\Form\Element\Fieldset;
use Magento\Framework\Data\Form\Element\AbstractElement as AE;

/**
 * 2016-09-06
 * @return string
 */
function df_fa() {return 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.css';}

/**
 * 2016-11-30
 * @return string
 */
function df_fa_link() {return df_link_inline(df_fa());}

/**
 * 2016-01-29
 * @param AE|E $e
 * @param string|null $k [optional]
 * @param string|null|callable $d [optional]
 * @return string|null|array(string => mixed)
 */
function df_fe_fc(AE $e, $k = null, $d = null) {return dfak(df_fe_top($e)->getFieldConfig(), $k, $d);}

/**
 * 2016-05-30
 * @param AE|E $e
 * @param string $key
 * @param bool|null|callable $default [optional]
 * @return bool
 */
function df_fe_fc_b(AE $e, $key, $default = false) {return df_bool(df_fe_fc($e, $key, $default));}

/**
 * 2016-11-13
 * @param AE|E $e
 * @param string $key
 * @param int|null|callable $default [optional]
 * @return string[]
 */
function df_fe_fc_csv(AE $e, $key, $default = 0) {return df_csv_parse(df_fe_fc($e, $key, $default));}

/**
 * 2016-01-29
 * @param AE|E $e
 * @param string $key
 * @param int|null|callable $default [optional]
 * @return int
 */
function df_fe_fc_i(AE $e, $key, $default = 0) {return df_int(df_fe_fc($e, $key, $default));}

/**
 * 2016-01-29
 * К сожалению, нельзя использовать @see is_callable(),
 * потому что эта функция всегда вернёт true из-за наличия магического метода
 * @see \Magento\Framework\DataObject::__call()
 * @param AE|E|Fieldset $e
 * @return AE|E
 */
function df_fe_top(AE $e) {return method_exists($e, 'top') ? $e->top() : $e;}

/**
 * 2015-11-28
 * @param AE|E $e
 * @param string|null $class [optional]
 * @param string|string[] $css [optional]
 * @param array(string => string) $params [optional]
 * @param string|null $path [optional]
 * @return void
 */
function df_fe_init(AE $e, $class = null, $css = [], $params = [], $path = null) {
	$class = df_cts($class ?: $e);
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
	// Используем df_ccc, чтобы отбросить $path, равный пустой строке.
	// Если имя класса заканчивается на FormElement, то это окончание в пути к ресурсу отбрасываем.
	$path = df_ccc('/', 'formElement', $path, 'main');
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
	/**
	 * 2016-03-08
	 * Отныне getBeforeElementHtml() будет гарантированно вызываться благодаря
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterGetElementHtml()
	 */
	$e['before_element_html'] .= df_cc_n(
		!df_asset_exists($path, $moduleName, 'js') ? null : df_x_magento_init(
			$moduleName, $path, ['id' => $e->getHtmlId()] + df_clean($params)
		)
		,df_link_inline($css)
	);
}

/**
 * 2016-08-10
 * «groups[all_pay][groups][installment_sales][fields][plans][template][months]» => «months»
 * @param string $nameFull
 * @return string
 */
function df_fe_name_short($nameFull) {return
	df_last(df_clean(df_explode_multiple(['[', ']'], $nameFull)))
;}

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
function df_fe_attrs(AE $e, $uidSuffix = null) {return
	['id' => $e->getHtmlId(), 'name' => $e->getName()]
	+ df_fe_uid($e, $uidSuffix)
	+ dfa_select($e->getData(), $e->getHtmlAttributes())
;}

/**
 * 2015-12-14
 * @param AE|E $e
 * @return void
 */
function df_hide(AE $e) {$e->setContainerClass('df-hidden');}


