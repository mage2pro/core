<?php
use Df\Core\Exception as DFE;
use Df\Framework\Form\Element as E;
use Df\Framework\Form\Element\Fieldset as DFS;
use Magento\Framework\Data\Form\Element\Fieldset as FS;
use Magento\Framework\Data\Form\Element\AbstractElement as AE;

/**
 * 2016-09-06    
 * @used-by df_fa_link()
 * @used-by \Df\Framework\Form\Element\ArrayT::onFormInitialized()
 * @used-by \Df\Framework\Form\Element\Font::onFormInitialized()
 * @return string
 */
function df_fa() {return 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.css';}

/**
 * 2016-11-30  
 * @used-by \Df\Sso\Button::loggedOut()
 * @return string
 */
function df_fa_link() {return df_link_inline(df_fa());}

/**
 * 2016-01-29
 * 2017-08-10
 * This function returns a form's field / group / fieldset configuration, not the field's value.
 * If you need to get a field's value, use @see df_fe_sibling_v().
 * E.g., `df_fe_top($e)->getFieldConfig()` can return:
 *		{
 *			"_elementType": "field",
 *			"depends": {
 *				"fields": {
 *					"enable": {
 *						"_elementType": "field",
 *						"dependPath": ["df_payment", "moip", "common", "enable"],
 *						"id": "df_payment/moip/common/enable",
 *						"value": "1"
 *					}
 *				}
 *			},
 *			"id": "webhooks",
 *			"label": "Webhooks",
 *			"path": "df_payment/moip/common",
 *			"showInDefault": "1",
 *			"showInStore": "1",
 *			"showInWebsite": "1",
 *			"sortOrder": "9",
 *			"translate": "label",
 *			"type": "Dfe\\Moip\\FE\\Webhooks",
 *			"value": [
 *				"\n\t\t\t\t\t\t",
 *				"\n\t\t\t\t\t\t",
 *				"\n\t\t\t\t\t"
 *			]
 *		}
 * 
 * @used-by \Df\Framework\Form\Element\Url::url()
 * @used-by \Df\Sso\FE\CustomerReturn::url()
 * @param AE|E $e
 * @param string|null $k [optional]
 * @param string|null|callable $d [optional]
 * @return string|null|array(string => mixed)
 */
function df_fe_fc(AE $e, $k = null, $d = null) {return dfak(df_fe_top($e)->getFieldConfig(), $k, $d);}

/**
 * 2016-05-30
 * @used-by \Df\Framework\Form\Element\Multiselect::ordered()
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
 * 2017-04-12 Видимо, @see df_fe_top() надо заменить на эту функцию.
 * 2017-04-23 Эта функция перестала кем-либо использоваться. Раньше она использовалась функцией @see df_fe_m().
 * @param AE|E $e
 * @return FS
 * @throws DFE
 */
function df_fe_fs(AE $e) {
	while ($e && !$e instanceof FS) {
		$e = $e->getContainer();
	}
	return df_assert($e);
}

/**
 * 2015-11-28
 * @used-by \Dfe\Dynamics365\Button::onFormInitialized()
 * @param AE|E $e
 * @param string|object|null $class [optional]
 * $class could be:
 * 1) A class name: «A\B\C».
 * 2) An object. It is reduced to case 1 via @see get_class()
 * @param string|string[] $css [optional]
 * @param array(string => string) $params [optional]
 * @param string|null $path [optional]
 */
function df_fe_init(AE $e, $class = null, $css = [], $params = [], $path = null) {
	$class = df_cts($class ?: $e);
	$moduleName = df_module_name($class); /** @var string $moduleName */
	// 2015-12-29
	// Мы различаем ситуации, когда $path равно null и пустой строке.
	// *) null означает, что имя ресурса должно определяться по имени класса.
	// *) пустая строка означает, что ресурс не имеет префикса, т.е. его имя просто «main».
	if (is_null($path)) {
		$classA = df_explode_class_lc($class); /** @var string[] $classA */
		$classLast = array_pop($classA);
		switch ($classLast) {
			// Если имя класса заканчивается на FormElement,
			// то это окончание в пути к ресурсу отбрасываем.
			case 'formElement':
			case 'fE': // 2018-04-19
				break; // $path будет равно null
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
	 * @see \Dfe\CurrencyFormat\FE обходится в настоящее время без CSS.
	 */
	if (df_asset_exists($path, $moduleName, 'less')) {
		$css[]= df_asset_name($path, $moduleName, 'css');
	}
	/**
	 * 2016-03-08
	 * Отныне getBeforeElementHtml() будет гарантированно вызываться благодаря
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterGetElementHtml()
	 * 2017-08-10
	 * I have removed @see df_clean() for $params.
	 * The previous edition: https://github.com/mage2pro/core/blob/2.10.11/Framework/lib/form.php#L177
	 */
	$e['before_element_html'] .= df_cc_n(
		!df_asset_exists($path, $moduleName, 'js') ? null : df_js(
			$moduleName, $path, ['id' => $e->getHtmlId()] + $params
		)
		,df_link_inline($css)
	);
}

/**
 * 2017-04-12
 * 2017-04-23
 * Добавил поддержку вложенных групп.
 * Теперь алгоритм поднимается по иерархии групп, пока не встретит тег «dfExtension».
 * @used-by \Df\Config\Fieldset::_getHeaderCommentHtml()
 * @used-by \Df\Framework\Form\Element\Url::m()
 * @param AE|E $e
 * @param bool $throw [optional]
 * @return string|null
 * @throws DFE
 */
function df_fe_m(AE $e, $throw = true) {
	/** @var AE|E $original */ $original = $e;
	/** @var string|null $r */ $r = null;
	while ($e && (!$e instanceof FS || !($r = dfa_deep($e->getData(), 'group/dfExtension')))) {
		$e = $e->getContainer();
	}
	return $r ?: (!$throw ? null : df_error(
		"«dfExtension» tag is absent for the «{$original->getId()}» configuration element."
	));
}

/**
 * 2016-08-10
 * «groups[all_pay][groups][installment_sales][fields][plans][template][months]» => «months»
 * @param string $nameFull
 * @return string
 */
function df_fe_name_short($nameFull) {return df_last(df_clean(df_explode_multiple(['[', ']'], $nameFull)));}

/**
 * 2017-08-10
 * @used-by df_fe_sibling_v()
 * @param AE $e
 * @param string $name
 * @return AE|null
 */
function df_fe_sibling(AE $e, $name) {return $e->getForm()->getElement(
	str_replace('/', '_', $e['field_config']['path']) . "_$name"
);}

/**
 * 2017-08-10
 * @used-by \Dfe\Moip\FE\Webhooks::onFormInitialized()
 * @param AE $e
 * @param string $name
 * @return mixed
 */
function df_fe_sibling_v(AE $e, $name) {return df_fe_sibling($e, $name)['value'];}

/**
 * 2016-01-29
 * К сожалению, нельзя использовать @see is_callable(),
 * потому что эта функция всегда вернёт true из-за наличия магического метода
 * 2017-04-12
 * Видимо, эту функцию надо заменить на @see df_fe_fs().
 * @see \Magento\Framework\DataObject::__call()
 * @param AE|E|DFS $e
 * @return AE|E
 */
function df_fe_top(AE $e) {return method_exists($e, 'top') ? $e->top() : $e;}

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
 */
function df_hide(AE $e) {$e->setContainerClass('df-hidden');}