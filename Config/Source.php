<?php
namespace Df\Config;
/**
 * 2015-11-14 Descendants of this class are not singletons because of  @see \Df\Config\Plugin\Model\Config\SourceFactory
 * 2017-03-28
 * This class should be a descendant of @see \Magento\Framework\DataObject to retrieve the `path` property value:
 * @see \Df\Config\Source::setPath()
 * @see \Magento\Config\Model\Config\Structure\Element\Field::_getOptionsFromSourceModel()
 *		$sourceModel = $this->_sourceFactory->create($sourceModel);
 *		if ($sourceModel instanceof \Magento\Framework\DataObject) {
 *			$sourceModel->setPath($this->getPath());
 *		}
 * https://github.com/magento/magento2/blob/2.1.5/app/code/Magento/Config/Model/Config/Structure/Element/Field.php#L435-L438
 *
 * @see \Df\Config\Source\API
 * @see \Df\Config\Source\Block
 * @see \Df\Config\Source\LetterCase
 * @see \Df\Config\Source\NoWhiteBlack
 * @see \Df\Config\Source\SizeUnit
 * @see \Df\Config\Source\WaitPeriodType
 * @see \Df\GingerPaymentsBase\Source\Option
 * @see \Df\Payment\Metadata
 * @see \Df\Payment\Source
 * @see \Df\Payment\Source\AC
 * @see \Df\Payment\Source\Identification
 * @see \Df\Payment\Source\Options\DisplayMode
 * @see \Df\Sso\Source\Button\Type\UL
 * @see \Dfe\AllPay\Source\Option
 * @see \Dfe\AmazonLogin\Source\Button\Native\Color
 * @see \Dfe\AmazonLogin\Source\Button\Native\Size
 * @see \Dfe\AmazonLogin\Source\Button\Native\Type
 * @see \Dfe\CheckoutCom\Source\Prefill
 * @see \Dfe\FacebookLogin\Source\Button\Size
 * @see \Dfe\IPay88\Source\Option
 * @see \Dfe\Moip\Source\Prefill
 * @see \Dfe\Omise\Source\Prefill
 * @see \Dfe\Paymill\Source\Prefill
 * @see \Dfe\Portal\Source\Content
 * @see \Dfe\PostFinance\Source\Hash\Algorithm
 * @see \Dfe\SecurePay\Source\ForceResult
 * @see \Dfe\Spryng\Source\Prefill
 * @see \Dfe\Square\Source\Location
 * @see \Dfe\TwoCheckout\Source\Prefill
 * @see \Dfe\Vantiv\Source\Environment
 * @see \Dfe\YandexKassa\Source\Option
 * @see \Dfe\ZohoCRM\Source\Domain
 * @see \Justuno\M2\Source\Brand
 * @see \KingPalm\B2B\Source\Type
 */
abstract class Source extends SourceBase {
	/**
	 * 2015-11-14
	 * @used-by toOptionArray()
	 * @see \Df\Config\Source\API::map()
	 * @see \Df\Config\Source\Block::map()
	 * @see \Df\Config\Source\LetterCase::map()
	 * @see \Df\Config\Source\NoWhiteBlack::map()
	 * @see \Df\Config\Source\SizeUnit::map()
	 * @see \Df\Config\Source\WaitPeriodType::map()
	 * @see \Df\GingerPaymentsBase\Source\Option::map()
	 * @see \Df\Payment\Metadata::map()
	 * @see \Df\Payment\Source\AC::map()
	 * @see \Df\Payment\Source\Identification::map()
	 * @see \Df\Payment\Source\Options\DisplayMode::map()
	 * @see \Df\Payment\Source\Options\Location::map()
	 * @see \Df\Sso\Source\Button\Type\UL::map()
	 * @see \Df\Sso\Source\Button\Type\UNL::map()
	 * @see \Dfe\AllPay\Source\Option::map()
	 * @see \Dfe\AmazonLogin\Source\Button\Native\Color::map()
	 * @see \Dfe\AmazonLogin\Source\Button\Native\Size::map()
	 * @see \Dfe\AmazonLogin\Source\Button\Native\Type::map()
	 * @see \Dfe\CheckoutCom\Source\Prefill::map()
	 * @see \Dfe\FacebookLogin\Source\Button\Size::map()
	 * @see \Dfe\IPay88\Source\Option::map()
	 * @see \Dfe\Moip\Source\Prefill::map()
	 * @see \Dfe\Omise\Source\Prefill::map()
	 * @see \Dfe\Paymill\Source\Prefill\With3DS::map()
	 * @see \Dfe\Paymill\Source\Prefill\Without3DS::map()
	 * @see \Dfe\Portal\Source\Content::map()
	 * @see \Dfe\PostFinance\Source\Hash\Algorithm::map()
	 * @see \Dfe\SecurePay\Source\ForceResult::map()
	 * @return array(<value> => <label>)
	 */
	abstract protected function map():array;
	/**
	 * 2016-07-05
	 * @used-by \Df\Payment\Settings\Options::denied()
	 * @used-by \KingPalm\B2B\Block\Registration::_toHtml()
	 * @see \Df\Payment\Metadata::keys()
	 * @return string[]
	 */
	function keys() {return dfc($this, function() {return array_keys($this->map());});}

	/**
	 * 2016-08-07
	 * @used-by \Df\GingerPaymentsBase\Source\Option::optionsTest()
	 * @used-by \Df\Payment\Settings\Options::o()
	 * @param string[]|null $k [optional]
	 * @return array(<value> => <label>)
	 */
	final function options($k = null) {return df_translate_a(dfa($this->map(), $k));}

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
	 * @used-by getAllOptions()
	 * @used-by \Magento\Config\Model\Config\Structure\Element\Field::_getOptionsFromSourceModel()
	 * @return array(array('label' => string, 'value' => int|string))
	 */
	final function toOptionArray() {return df_map_to_options_t($this->map());}

	/**
	 * 2015-11-14
	 * It returns the value of the attribute $k or the child node $k.
	 * Let we have a field:
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
	 * Then df_config_field()->getData() returns an array which contais all attributes and children of `<field>`:
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
	 * And f() allows us to retrieve a particular attribute or a child.
	 * @used-by \Df\Config\Source\LetterCase::map()
	 * @param string $k
	 * @return string|null
	 */
	final protected function f($k) {return df_config_field()->getAttribute($k);}

	/**
	 * 2017-04-10 «all_pay»
	 * @used-by \Df\Payment\Source::titleB()
	 * @param string $k
	 * @return string
	 */
	final protected function sibling($k) {return df_cfg(df_cc_path(df_head($this->pathA()), $k), df_scope());}

	/**
	 * 2017-03-28
	 * @used-by sibling()
	 * @used-by \Df\Payment\Source\API\Key\Testable::_test()
	 * @used-by \Df\ZohoBI\Source\Organization::app()
	 * @return string[]
	 */
	final protected function pathA() {return dfc($this, function() {return df_explode_path($this->_path);});}

	/**
	 * 2016-07-12
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Dfe\IPay88\ConfigProvider::options()
	 * @used-by \Dfe\IPay88\W\Event::optionTitle()
	 * @used-by \KingPalm\B2B\Block\Registration::_toHtml()
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
	final protected static function addKeysToValues(array $a) {return df_map_k($a, function($k, $v) {return "$v: $k";});}

	/**
	 * 2017-03-28
	 * @used-by pathA()
	 * @used-by setPath()
	 * @var string
	 */
	private $_path;
}