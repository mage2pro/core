<?php
use Df\Config\Model\Config\Structure as DfStructure;
use Magento\Config\Model\Config\Structure;
use Magento\Config\Model\Config\Structure\Element\Field;
use Magento\Config\Model\Config\Structure\Element\Group;
use Magento\Config\Model\Config\Structure\Element\Section;
use Magento\Config\Model\Config\Structure\Element\Tab;
use Magento\Config\Model\Config\Structure\ElementInterface as IElement;
use Magento\Framework\Phrase;
/**
 * 2016-08-02
 * *) По аналогии с @see \Magento\Config\Block\System\Config\Form::initForm()
 * *) Мы не можем кэшировать результат, потому что возвращаемые объекты - это приспособленцы (fleweights).
 * *) Метод не может вернуть обект класса @see \Magento\Config\Model\Config\Structure\Element\Tab
 * потому что идентификатор вкладки не входит в $path.
 * Для получения данных вкладки используйте метод @see \Df\Config\Model\Config\Structure::tab()
 * Для получения названия вкладки используйте функцию @see df_config_tab_label()
 *
 * @param string $path
 * @param bool $throw [optional]
 * @param string|null $expectedClass [optional]
 * @return IElement|Field|Group|Section|null
 */
function df_config_e($path, $throw = true, $expectedClass = null) {
	/** @var IElement|Field|Group|Section|null $result */
	$result = df_config_structure()->getElement($path);
	if (!$result && $throw) {
		df_error_html(__("Unable to read the configuration node «<b>%1</b>»", $path));
	}
	return !$result || !$expectedClass || $result instanceof $expectedClass ? $result :
		df_error_html(__(
			"The configuation node «<b>%1</b>» should be an instance of the <b>%2</b> class, "
			."but actually it is an instance of the <b>%3</b> class."
			, $path, $expectedClass, df_cts($result)
		))
	;
}

/**
 * 2016-08-02
 * @param string $path
 * @param bool $throw [optional]
 * @return Field|null
 */
function df_config_field($path, $throw = true) {return df_config_e($path, $throw, Field::class);}

/**
 * 2016-08-02
 * @param string $path
 * @param bool $throw [optional]
 * @return Group|null
 */
function df_config_group($path, $throw = true) {return df_config_e($path, $throw, Group::class);}

/**
 * 2016-08-02
 * @param string $path
 * @param bool $throw [optional]
 * @return Section|null
 */
function df_config_section($path, $throw = true) {return df_config_e($path, $throw, Section::class);}

/**
 * 2016-08-02
 * По аналогии с @see \Magento\Config\Block\System\Config\Form::__construct()
 * @return Structure
 */
function df_config_structure() {return df_o(Structure::class);}

/**
 * 2016-08-02
 * @param Section $section
 * @return Phrase
 */
function df_config_tab_label(Section $section) {
	return DfStructure::tab($section->getData()['tab'], 'label');
}
