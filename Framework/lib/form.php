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
 */
function df_fa():string {return 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.css';}

/**
 * 2016-11-30  
 * @used-by \Df\Sso\Button::loggedOut()
 */
function df_fa_link():string {return df_link_inline(df_fa());}

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
 * 2019-05-31
 * The `field_config` field is initialized here:
 * \Magento\Config\Model\PreparedValueFactory::create():
 *		if ($field instanceof Structure\Element\Field) {
 *			$groupPath = $field->getGroupPath();
 *			$group = $structure->getElement($groupPath);
 *			$backendModel->setField($field->getId());
 *			$backendModel->setGroupId($group->getId());
 *			$backendModel->setFieldConfig($field->getData());
 *		}
 * https://github.com/magento/magento2/blob/2.3.1/app/code/Magento/Config/Model/PreparedValueFactory.php#L129-L135
 * So the `field_config` field is initialized only for the backend configuration screens,
 * and I have added the `?: []` case now.
 * @used-by df_fe_fc_b()
 * @used-by df_fe_fc_csv()
 * @used-by df_fe_fc_i()
 * @used-by \Df\Framework\Form\Element\Fieldset::fc()
 * @used-by \Df\Framework\Form\Element\Url::url()
 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterGetComment()
 * @used-by \Df\Sso\FE\CustomerReturn::url()
 * @param AE|E $e
 * @param string|null $k [optional]
 * @param string|null|callable $d [optional]
 * @return string|null|array(string => mixed)
 */
function df_fe_fc(AE $e, $k = null, $d = null) {return dfa(df_eta(df_fe_top($e)->getFieldConfig()), $k, $d);}

/**
 * 2016-05-30
 * @used-by \Df\Framework\Form\Element\Multiselect::ordered()
 * @param AE|E $e
 * @param bool|null|callable $d [optional]
 */
function df_fe_fc_b(AE $e, string $key, $d = false):bool {return df_bool(df_fe_fc($e, $key, $d));}

/**
 * 2016-11-13
 * @used-by \Df\Directory\FE\Dropdown::dfValues()
 * @param AE|E $e
 * @param int|null|callable $d [optional]
 * @return string[]
 */
function df_fe_fc_csv(AE $e, string $key, $d = 0):array {return df_csv_parse(df_fe_fc($e, $key, $d));}

/**
 * 2016-01-29
 * @param AE|E $e
 * @param int|null|callable $d [optional]
 */
function df_fe_fc_i(AE $e, string $key, $d = 0):int {return df_int(df_fe_fc($e, $key, $d));}

/**
 * 2017-04-12 Видимо, @see df_fe_top() надо заменить на эту функцию.
 * 2017-04-23 Эта функция перестала кем-либо использоваться. Раньше она использовалась функцией @see df_fe_m().
 * @param AE|E $e
 * @throws DFE
 */
function df_fe_fs(AE $e):FS {
	while ($e && !$e instanceof FS) {
		$e = $e->getContainer();
	}
	return df_assert($e);
}

/**
 * 2015-11-28
 * @used-by \Df\Directory\FE\Dropdown::onFormInitialized()
 * @used-by \Df\Framework\Form\Element\ArrayT::onFormInitialized()
 * @used-by \Df\Framework\Form\Element\Color::onFormInitialized()
 * @used-by \Df\Framework\Form\Element\Fieldset::onFormInitialized()
 * @used-by \Df\Framework\Form\Element\Font::onFormInitialized()
 * @used-by \Df\Framework\Form\Element\GoogleFont::onFormInitialized()
 * @used-by \Df\Framework\Form\Element\Multiselect::onFormInitialized()
 * @used-by \Df\Framework\Form\Element\Number::onFormInitialized()
 * @used-by \Df\Framework\Form\Element\Quantity::onFormInitialized()
 * @used-by \Df\Framework\Form\Element\Select2::setRenderer()
 * @used-by \Df\Framework\Form\Element\Select2\Number::onFormInitialized()
 * @used-by \Df\Framework\Form\Element\Table::onFormInitialized()
 * @used-by \Df\OAuth\FE\Button::onFormInitialized()
 * @used-by \Dfe\AllPay\InstallmentSales\Plan\FE::onFormInitialized()
 * @used-by \Dfe\CurrencyFormat\FE::onFormInitialized()
 * @used-by \Dfe\Dynamics365\Button::onFormInitialized()
 * @used-by \Dfe\Moip\FE\Webhooks::onFormInitialized()
 * @used-by \Dfe\SalesSequence\Config\Matrix\Element::onFormInitialized()
 * @used-by \Dfe\Sift\PM\FE::onFormInitialized()
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
	# 2015-12-29
	# Мы различаем ситуации, когда $path равно null и пустой строке.
	# *) null означает, что имя ресурса должно определяться по имени класса.
	# *) пустая строка означает, что ресурс не имеет префикса, т.е. его имя просто «main».
	if (is_null($path)) {
		$classA = df_explode_class_lc($class); /** @var string[] $classA */
		$classLast = array_pop($classA);
		switch ($classLast) {
			# Если имя класса заканчивается на FormElement,
			# то это окончание в пути к ресурсу отбрасываем.
			case 'formElement':
			case 'fE': # 2018-04-19
				break; # $path будет равно null
			# Если имя класса заканчивается на Element,
			# то в качестве пути к ресурсу используем предыдущую часть класса.
			# Пример: «Dfe\SalesSequence\Config\Matrix\Element» => «matrix»
			case 'element':
				$path = array_pop($classA);
				break;
			default:
				$path = $classLast;
		}
	}
	# 2015-12-29
	# Используем df_ccc, чтобы отбросить $path, равный пустой строке.
	# Если имя класса заканчивается на FormElement, то это окончание в пути к ресурсу отбрасываем.
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
		!df_asset_exists($path, $moduleName, 'js') ? null : df_js($moduleName, $path, ['id' => $e->getHtmlId()] + $params)
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
 * @return string|null
 * @throws DFE
 */
function df_fe_m(AE $e, bool $throw = true) {  /** @var string|null $r */
	$r = null;
	$orig = $e; /** @var AE|E $orig */
	while ($e && (!$e instanceof FS || !($r = dfa_deep($e->getData(), 'group/dfExtension')))) {
		$e = $e->getContainer();
	}
	return $r ?: (!$throw ? null : df_error("«dfExtension» tag is absent for the «{$orig->getId()}» configuration element."));
}

/**
 * 2016-08-10 «groups[all_pay][groups][installment_sales][fields][plans][template][months]» => «months»
 * @used-by \Df\Framework\Form\Element\Select2::setRenderer()
 */
function df_fe_name_short(string $full):string {return df_last(df_clean(df_explode_multiple(['[', ']'], $full)));}

/**
 * 2017-08-10
 * @used-by df_fe_sibling_v()
 * @return AE|null
 */
function df_fe_sibling(AE $e, string $n) {return $e->getForm()->getElement(
	str_replace('/', '_', $e['field_config']['path']) . "_$n"
);}

/**
 * 2017-08-10
 * @used-by \Dfe\Moip\FE\Webhooks::onFormInitialized()
 * @return mixed
 */
function df_fe_sibling_v(AE $e, string $n) {return df_fe_sibling($e, $n)['value'];}

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
 * @return array(string => string)
 */
function df_fe_uid(AE $e, string $suffix = ''):array {return ['data-ui-id' => E::uidSt($e, $suffix)];}

/**
 * 2016-01-08
 * @used-by \Df\Framework\Form\Element\Checkbox::getElementHtml()
 * @used-by \Dfe\Markdown\FormElement::componentHtml()
 * @return array(string => string)
 */
function df_fe_attrs(AE $e, string $uidSuffix = ''):array {return
	['id' => $e->getHtmlId(), 'name' => $e->getName()]
	+ df_fe_uid($e, $uidSuffix)
	+ dfa($e->getData(), $e->getHtmlAttributes())
;}

/**
 * 2015-12-14
 * @used-by \Df\Framework\Form\Element\ArrayT::onFormInitialized()
 * @used-by \Df\Framework\Form\Element\Fieldset::hide()
 * @used-by \Df\Framework\Form\Element\Font::onFormInitialized()
 * @param AE|E $e
 */
function df_hide(AE $e):void {$e->setContainerClass('df-hidden');}