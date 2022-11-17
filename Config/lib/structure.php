<?php
use Df\Config\Model\Config\Structure as DfStructure;
use Magento\Config\Model\Config\Structure;
use Magento\Config\Model\Config\Structure\Element\Field as F;
use Magento\Config\Model\Config\Structure\Element\Group;
use Magento\Config\Model\Config\Structure\Element\Section;
use Magento\Config\Model\Config\Structure\ElementInterface as IElement;
use Magento\Framework\App\Config\Value as V;
/**
 * 2016-08-02
 * *) By analogy with @see \Magento\Config\Block\System\Config\Form::initForm()
 * *) Мы не можем кэшировать результат, потому что возвращаемые объекты - это приспособленцы (fleweights).
 * *) Метод не может вернуть объект класса @see \Magento\Config\Model\Config\Structure\Element\Tab
 * потому что идентификатор вкладки не входит в $path.
 * Для получения данных вкладки используйте метод @see \Df\Config\Model\Config\Structure::tab()
 * Для получения названия вкладки используйте функцию @see df_config_tab_label()
 * @used-by df_config_field()
 * @used-by df_config_group()
 * @used-by df_config_section()
 * @param V|string $path
 * @param bool $throw [optional]
 * @param string|null $expectedClass [optional]
 * @return IElement|F|Group|Section|null
 */
function df_config_e($path, $throw = true, $expectedClass = null) { /** @var IElement|F|Group|Section|null $r */
	# 2020-02-02
	# 1) It correctly handles a custom `config_path` value.
	# 2) "Magento\Config\Model\Config\Structure\AbstractElement::getPath() ignores a custom `config_path` value"
	# https://mage2.pro/t/5148
	if ($path instanceof V) {
		$c = $path['field_config']; /** @var array(string => mixed) $c */
		$path = df_cc_path($c['path'], $c['id']);
	}
	if (!($r = df_config_structure()->getElement($path)) && $throw) {
		df_error_html(__("Unable to read the configuration node «<b>%1</b>»", $path));
	}
	return !$r || !$expectedClass || $r instanceof $expectedClass ? $r : df_error_html(__(
		"The configuation node «<b>%1</b>» should be an instance of the <b>%2</b> class,"
		." but actually it is an instance of the <b>%3</b> class."
		, $path, $expectedClass, df_cts($r)
	));
}

/**
 * 2015-11-14
 * 2017-06-29
 * В контексте @used-by \Df\Config\Backend::fc() при загрузке настроек эта функция работает правильно,
 * проверил в отладчике.
 * @used-by df_config_field_path()
 * @used-by \Df\Config\Backend::fc()
 * @used-by \Df\Config\Source::f()
 * @param V|null $v [optional]
 */
function df_config_field(V $v = null):F {/** @var F $r */
	/**
	 * 2015-11-14
	 * Очень красивое и неожиданное решение.
	 * Оказывается, Magento 2 использует для настроечных полей шаблон проектирования «Приспособленец»:
	 * https://ru.wikipedia.org/wiki/Приспособленец_(шаблон_проектирования)
	 * Поэтому настроечное поле является объектом-одиночкой, и мы можем получить его из реестра.
	 * https://mage2.pro/t/212
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
	 * 3)
	 * @see \Magento\Config\Model\Config\Structure\Element\Iterator::current()
	 * 		function current() {return $this->_flyweight;}
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.1/app/code/Magento/Config/Model/Config/Structure/Element/Iterator.php#L71-L74
	 * @see \Magento\Config\Model\Config\Structure\Element\Iterator::next()
	 *	function next() {
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
	$r = df_action_is('adminhtml_system_config_edit') ? df_o(F::class) : null; /** @var F $r */
	/**
	 * 2020-02-02
	 * @uses df_config_e() returns an incomplete F object if the field uses a custom `config_path` value.
	 * I presume it is related to the issue:
	 * "Magento\Config\Model\Config\Structure\AbstractElement::getPath() ignores a custom `config_path` value"
	 * https://mage2.pro/t/5148
	 * The code below is a workaround.
	 * 2020-07-08
	 * The previous code was just wrong:
	 *		if (!$v || $r && ($cp = $r->getConfigPath()) && $cp === $v->getPath()) {
	 *			df_assert($r && $r->getData());
	 *		}
	 * https://github.com/mage2pro/core/blob/6.7.2/Config/lib/structure.php#L121
	 * Notice that the $cp variable was double initialized and never used.
	 * The wrong code led to the error:
	 * «The required parameter `dfEntity` is absent for the `df_payment/all_pay/installment_sales/plans` field»:
	 * https://github.com/mage2pro/core/issues/106
	 */
	if (!$v || $r && !$r->getConfigPath()) {
		df_assert($r && $r->getData() && $r->getPath());
	}
	else {
		/**
		 * 2017-06-29
		 * We CAN NOT CACHE the result because it is a flyweight:
		 * @see \Magento\Config\Model\Config\Structure::getElementByPathParts():
		 * 		$this->_elements[$path] = $this->_flyweightFactory->create($child['_elementType']);
		 * @see \Magento\Config\Model\Config\Structure\Element\FlyweightFactory::create():
		 * 		return $this->_objectManager->create($this->_flyweightMap[$type]);
		 */
		$r = df_config_e($v);
	}
	return $r;
}

/**
 * 2017-12-12
 * 1) "@uses \Magento\Config\Model\Config\Structure\AbstractElement::getPath()
 * ignores a custom `config_path` value": https://mage2.pro/t/5148
 * 2) @uses df_config_field() returns a flyweight: https://en.wikipedia.org/wiki/Flyweight_pattern
 * @used-by \Df\Config\Comment::groupPath()
 */
function df_config_field_path():string {
	$f = df_config_field(); /** @var F $f */
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
 * 2020-02-02 @deprecated It is unused.
 * @param string $path
 * @param bool $throw [optional]
 * @return Section|null
 */
function df_config_section($path, $throw = true) {return df_config_e($path, $throw, Section::class);}

/**
 * 2016-08-02 By analogy with @see \Magento\Config\Block\System\Config\Form::__construct()
 * @used-by df_config_e()
 * @used-by \Df\Config\Backend::label()
 * @used-by \Df\Config\Model\Config\Structure::tab()
 */
function df_config_structure():Structure {return df_o(Structure::class);}

/**
 * 2016-08-02
 * @used-by \Df\Config\Backend::label()
 */
function df_config_tab_label(Section $s):string {return DfStructure::tab($s->getData()['tab'], 'label');}