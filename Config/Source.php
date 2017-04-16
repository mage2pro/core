<?php
namespace Df\Config;
use Magento\Config\Model\Config\Structure\Element\Field;
use Magento\Framework\DataObject as Ob;
/**
 * 2015-11-14
 * Благодаря @see \Df\Config\Plugin\Model\Config\SourceFactory
 * потомки этого класса не являются объектами-одиночками.
 * 2016-08-07
 * @see \Df\Config\Source\LetterCase
 * @see \Df\Config\Source\NoWhiteBlack
 * @see \Df\Config\Source\SizeUnit
 * @see \Df\Payment\Source
 * @see \Df\Payment\Source\Testable
 * @see \Df\PaypalClone\Source\Identification
 * @see \Df\GingerPaymentsBase\Source\Option
 * @see \Df\Payment\Metadata
 * @see \Df\Payment\Source\AC
 * @see \Df\Sso\Source\Button\Type\UL
 * @see \Dfe\AllPay\Source\Option
 * @see \Dfe\AllPay\Source\WaitPeriodType
 * @see \Dfe\AmazonLogin\Source\Button\Native\Color
 * @see \Dfe\AmazonLogin\Source\Button\Native\Size
 * @see \Dfe\AmazonLogin\Source\Button\Native\Type
 * @see \Dfe\CheckoutCom\Source\Prefill
 * @see \Dfe\FacebookLogin\Source\Button\Size
 * @see \Dfe\IPay88\Source\Option
 * @see \Dfe\MPay24\Source\Option
 * @see \Dfe\Omise\Source\Prefill
 * @see \Dfe\Paymill\Source\Prefill
 * @see \Dfe\SecurePay\Source\ForceResult
 * @see \Dfe\Spryng\Source\Prefill
 * @see \Dfe\Square\Source\Location
 * @see \Dfe\Stripe\Source\Prefill
 * @see \Dfe\TwoCheckout\Source\Prefill
 *
 * 2017-03-28
 * Мы вынуждены наследоваться от @see \Magento\Framework\DataObject,
 * чтобы получить от ядра значение «path»:
 * @see \Df\Config\Source::setPath()
 * @see \Magento\Config\Model\Config\Structure\Element\Field::_getOptionsFromSourceModel()
 *		$sourceModel = $this->_sourceFactory->create($sourceModel);
 *		if ($sourceModel instanceof \Magento\Framework\DataObject) {
 *			$sourceModel->setPath($this->getPath());
 *		}
 * https://github.com/magento/magento2/blob/2.1.5/app/code/Magento/Config/Model/Config/Structure/Element/Field.php#L435-L438
 */
abstract class Source extends Ob implements \Magento\Framework\Option\ArrayInterface {
	/**
	 * 2015-11-14
	 * @used-by \Df\Config\Source::toOptionArray()
	 * @return array(string => string)
	 */
	abstract protected function map();
	/**
	 * 2016-07-05
	 * @used-by \Df\Payment\Settings\Options::denied()
	 * @see \Df\Payment\Metadata::keys()
	 * @return string[]
	 */
	function keys() {return dfc($this, function() {return array_keys($this->map());});}

	/**
	 * 2016-08-07
	 * @used-by \Df\Payment\Settings\Options::o()
	 * @used-by \Df\GingerPaymentsBase\Source\Option::optionsTest()
	 * @param string[]|null $keys [optional]
	 * @return array(string => string)
	 */
	final function options($keys = null) {
		/** @var array(string => string) $o */
		$o = $this->map();
		return df_translate_a(is_null($keys) ? $o : dfa_select_ordered($o, $keys));
	}

	/**
	 * 2017-03-28
	 * @used-by \Magento\Config\Model\Config\Structure\Element\Field::_getOptionsFromSourceModel()
	 *		$sourceModel = $this->_sourceFactory->create($sourceModel);
	 *		if ($sourceModel instanceof \Magento\Framework\DataObject) {
	 *			$sourceModel->setPath($this->getPath());
	 *		}
	 * https://github.com/magento/magento2/blob/2.1.5/app/code/Magento/Config/Model/Config/Structure/Element/Field.php#L435-L438
	 * @param string $v
	 */
	final function setPath($v) {$this->_path = $v;}

	/**
	 * 2015-11-27
	 * @override
	 * @see \Magento\Framework\Option\ArrayInterface::toOptionArray()
	 * @used-by \Magento\Config\Model\Config\Structure\Element\Field::_getOptionsFromSourceModel()
	 * @return array(array(string => string))
	 */
	final function toOptionArray() {return df_map_to_options_t($this->map());}

	/**
	 * 2015-11-14
	 * Возаращает по имени атрибут или содержимое дочернего тега для настроечного поля.
	 * Например, пусть есть поле:
	 *		<field
	 *			id='visibility'
	 *			translate='label'
	 *			type='select'
	 *			sortOrder='1'
	 *			showInDefault='1'
	 *			showInWebsite='1'
	 *			showInStore='1'
	 *		>
	 *			<label>Visibility</label>
	 *			<source_model>Dfe\Sku\ConfigSource\Visibility</source_model>
	 *			<comment><![CDATA[<a href='https://mage2.pro/t/197'>Documentation.</a>]]></comment>
	 *		</field>
	 * Тогда
	 * $this->field()->getData() вернёт такой массив:
	 *	array(
	 *		[_elementType] => field
	 *		[comment] => <a href='https://mage2.pro/t/197'>Documentation.</a>
	 *		[id] => visibility
	 *		[label] => Visibility
	 *		[path] => dfe_sku/frontend
	 *		[showInDefault] => 1
	 *		[showInStore] => 1
	 *		[showInWebsite] => 1
	 *		[sortOrder] => 1
	 *		[source_model] => Dfe\Sku\ConfigSource\Visibility
	 *		[translate] => label
	 *		[type] => select
	 *	)
	 * Обратите внимание: массив содержит и атрибуты, и детей.
	 *
	 * @used-by \Df\Config\Source\LetterCase::map()
	 *
	 * @param string $k
	 * @return string|null
	 */
	final protected function f($k) {return $this->field()->getAttribute($k);}

	/**
	 * 2017-04-10 «all_pay»
	 * @used-by \Df\Payment\Source::titleB()
	 * @param string $k
	 * @return string
	 */
	final protected function sibling($k) {return df_cfg(df_cc_path(df_head($this->pathA()), $k));}

	/**
	 * 2017-03-28
	 * @used-by sibling()
	 * @used-by \Df\Payment\Source\Testable::_test()
	 * @return string[]
	 */
	final protected function pathA() {return dfc($this, function() {return df_explode_path(
		$this->_path
	);});}

	/**
	 * 2015-11-14
	 * Очень красивое и неожиданное решение.
	 * Оказывается, Magento 2 использует для настроечных полей
	 * шаблон проектирования «Приспособленец»:
	 * https://ru.wikipedia.org/wiki/Приспособленец_(шаблон_проектирования)
	 * Поэтому настроечное поле является объектом-одиночкой,
	 * и мы можем получить его из реестра.
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
	 * https://github.com/magento/magento2/blob/2335247d4ae2dc1e0728ee73022b0a244ccd7f4c/app/code/Magento/Config/Model/Config/Structure/Element/Iterator/Field.php#L30
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
	 * https://github.com/magento/magento2/blob/2335247d4ae2dc1e0728ee73022b0a244ccd7f4c/app/code/Magento/Config/Model/Config/Structure/Element/Group.php#L36
	 *
	 * 3)
	 * @see \Magento\Config\Model\Config\Structure\Element\Iterator::current()
	 *	function current()
	 *	{
	 *	return $this->_flyweight;
	 *	}
	 * https://github.com/magento/magento2/blob/2335247d4ae2dc1e0728ee73022b0a244ccd7f4c/app/code/Magento/Config/Model/Config/Structure/Element/Iterator.php#L68-L71
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
	 * https://github.com/magento/magento2/blob/2335247d4ae2dc1e0728ee73022b0a244ccd7f4c/app/code/Magento/Config/Model/Config/Structure/Element/Iterator.php#L78-L87
	 * @return Field
	 * @used-by f()
	 */
	private function field() {return dfc($this, function() {return df_o(Field::class);});}

	/**
	 * 2016-07-12
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Dfe\IPay88\ConfigProvider::config()
	 * @used-by \Dfe\IPay88\W\Event::optionTitle()
	 * @return self
	 */
	static function s() {return dfcf(function($c) {return new $c;}, [static::class]);}

	/**
	 * 2017-02-05
	 * @used-by \Dfe\Paymill\Source\Prefill\With3DS::map()
	 * @used-by \Dfe\Paymill\Source\Prefill\Without3DS::map()
	 * @used-by \Dfe\Spryng\Source\Prefill::map()
	 * @param array(string => string) $a
	 * @return array(string => string)
	 */
	final protected static function addKeysToValues(array $a) {return df_map_k(
		$a, function($k, $v) {return "$v: $k";}
	);}

	/**
	 * 2017-03-28
	 * @used-by pathA()
	 * @used-by setPath()
	 * @var string
	 */
	private $_path;
}