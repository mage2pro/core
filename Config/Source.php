<?php
namespace Df\Config;
use Magento\Config\Model\Config\Structure\Element\Field;
/**
 * 2015-11-14
 * Благодаря @see \Df\Config\Plugin\Model\Config\SourceFactory
 * потомки этого класса не являются объектами-одиночками.
 * 2016-08-07
 * @see \Df\Config\Source\LetterCase
 * @see \Df\Config\Source\NoWhiteBlack
 * @see \Df\Config\Source\SizeUnit
 * @see \Df\Payment\Source\Testable
 * @see \Df\GingerPaymentsBase\Source\Option
 * @see \Df\Payment\Metadata
 * @see \Df\Payment\Source\AC
 * @see \Df\Sso\Source\Button\Type\UL
 * @see \Dfe\AllPay\Source\Option
 * @see \Dfe\AllPay\Source\OptionsLocation
 * @see \Dfe\AllPay\Source\PaymentIdentificationType
 * @see \Dfe\AllPay\Source\WaitPeriodType
 * @see \Dfe\AmazonLogin\Source\Button\Native\Color
 * @see \Dfe\AmazonLogin\Source\Button\Native\Size
 * @see \Dfe\AmazonLogin\Source\Button\Native\Type
 * @see \Dfe\CheckoutCom\Source\Prefill
 * @see \Dfe\FacebookLogin\Source\Button\Size
 * @see \Dfe\Omise\Source\Prefill
 * @see \Dfe\Paymill\Source\Prefill
 * @see \Dfe\SecurePay\Source\ForceResult
 * @see \Dfe\Spryng\Source\Prefill
 * @see \Dfe\Square\Source\Location
 * @see \Dfe\Stripe\Source\Prefill
 * @see \Dfe\TwoCheckout\Source\Prefill
 */
abstract class Source implements \Magento\Framework\Option\ArrayInterface {
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
		/** @var array(string => string) $options */
		$options = $this->map();
		return df_translate_a(is_null($keys) ? $options : dfa_select_ordered($options, $keys));
	}

	/**
	 * 2015-11-27
	 * @override
	 * @see \Magento\Framework\Option\ArrayInterface::toOptionArray()
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
	 * @param string $key
	 * @return string|null
	 */
	final protected function f($key) {return $this->field()->getAttribute($key);}

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
	 * http://php.net/manual/function.get-called-class.php#115790
	 * @return self
	 */
	final static function s() {return dfcf(function($c) {return new $c;}, [static::class]);}

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
}