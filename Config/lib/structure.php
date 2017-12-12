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
 * *) By analogy with @see \Magento\Config\Block\System\Config\Form::initForm()
 * *) Мы не можем кэшировать результат, потому что возвращаемые объекты - это приспособленцы (fleweights).
 * *) Метод не может вернуть обект класса @see \Magento\Config\Model\Config\Structure\Element\Tab
 * потому что идентификатор вкладки не входит в $path.
 * Для получения данных вкладки используйте метод @see \Df\Config\Model\Config\Structure::tab()
 * Для получения названия вкладки используйте функцию @see df_config_tab_label()
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
 * 2015-11-14
 * 2017-06-29
 * В контексте @used-by \Df\Config\Backend::fc() при загрузке настроек эта функция работает правильно,
 * проверил в отладчике.
 * @used-by \Df\Config\Backend::fc()
 * @used-by \Df\Config\Comment::groupPath()
 * @used-by \Df\Config\Source::f()
 * @param string|null $path [optional]
 * @return Field
 */
function df_config_field($path = null) {
	/** @var Field $result */
	if ($path) {
		/**
		 * 2017-06-29
		 * We CAN NOT CACHE the result because it is a flyweight:
		 * @see \Magento\Config\Model\Config\Structure::getElementByPathParts():
		 * 		$this->_elements[$path] = $this->_flyweightFactory->create($child['_elementType']);
		 * @see \Magento\Config\Model\Config\Structure\Element\FlyweightFactory::create():
		 * 		return $this->_objectManager->create($this->_flyweightMap[$type]);
		 */
		$result = df_config_e($path);
	}
	else {
		/**
		 * 2017-06-29
		 * По сути, мы могли бы и не заводить эту ветку кода, а всегда использовать @uses df_config_e():
		 * эта функция возвращает тот же самый результат, что и наша ветка кода.
		 * В обоих случаях возвращается приспособленец (flyweight).
		 * Однако df_config_e() требует параметр $path, а наша ветка — не требует,
		 * но работает только в сценарии, когда запрашивается текущий field (т.е. path известен в этом случае).
		 * Только в этом и разница.
		 *
		 * Прошлый комментарий:
		 * 2015-11-14
		 * Очень красивое и неожиданное решение.
		 * Оказывается, Magento 2 использует для настроечных полей
		 * шаблон проектирования «Приспособленец»:
		 * https://ru.wikipedia.org/wiki/Приспособленец_(шаблон_проектирования)
		 * Поэтому настроечное поле является объектом-одиночкой, и мы можем получить его из реестра.
		 *
		 * https://mage2.pro/t/212
		 *
		 * 1)
		 * @see \Magento\Config\Model\Config\Structure\Element\Iterator\Field::__construct()
		 *	function __construct(
		 *		\Magento\Config\Model\Config\Structure\Element\Group $groupFlyweight,
		 *		\Magento\Config\Model\Config\Structure\Element\Field $fieldFlyweight
		 *	) {
		 *		$this->_groupFlyweight = $groupFlyweight;
		 *		$this->_fieldFlyweight = $fieldFlyweight;
		 *	}
		 * https://github.com/magento/magento2/blob/2.2.0-RC1.1/app/code/Magento/Config/Model/Config/Structure/Element/Iterator/Field.php#L30
		 *
		 * 2)
		 * @see \Magento\Config\Model\Config\Structure\Element\Group::__construct()
		 *	function __construct(
		 *		(...)
		 *		\Magento\Config\Model\Config\Structure\Element\Iterator\Field $childrenIterator,
		 *		(...)
		 *	) {
		 *		parent::__construct($storeManager, $moduleManager, $childrenIterator);
		 *		(...)
		 *	}
		 * https://github.com/magento/magento2/blob/2.2.0-RC1.1/app/code/Magento/Config/Model/Config/Structure/Element/Group.php#L40
		 *
		 * 3)
		 * @see \Magento\Config\Model\Config\Structure\Element\Iterator::current()
		 *	function current()
		 *	{
		 *	return $this->_flyweight;
		 *	}
		 * https://github.com/magento/magento2/blob/2.2.0-RC1.1/app/code/Magento/Config/Model/Config/Structure/Element/Iterator.php#L71-L74
		 *
		 * @see \Magento\Config\Model\Config\Structure\Element\Iterator::next()
		 *	function next()
		 *	{
		 *		next($this->_elements);
		 *		if (current($this->_elements)) {
		 *			$this->_initFlyweight(current($this->_elements));
		 *			if (!$this->current()->isVisible()) {
		 *				$this->next();
		 *			}
		 *		}
		 *	 }
		 * https://github.com/magento/magento2/blob/2.2.0-RC1.1/app/code/Magento/Config/Model/Config/Structure/Element/Iterator.php#L81-L90
		 */
		df_assert(df_action_is('adminhtml_system_config_edit'));
		$result = df_o(Field::class);
		df_assert($result->getData());
	}
	return $result;
}

/**
 * 2017-12-12
 * 1) "@uses \Magento\Config\Model\Config\Structure\AbstractElement::getPath()
 * ignores a custom `config_path` value": https://mage2.pro/t/5148
 * 2) @uses df_config_field() returns a flyweight: https://en.wikipedia.org/wiki/Flyweight_pattern
 * @used-by \Df\Config\Comment::groupPath()
 * @return string
 */
function df_config_field_path() {
	$f = df_config_field(); /** @var Field $f */
	return $f->getConfigPath() ?: $f->getPath();
}

/**
 * 2016-08-02
 * @used-by \Df\Payment\Comment\Description::locations()
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
 * By analogy with @see \Magento\Config\Block\System\Config\Form::__construct()
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
