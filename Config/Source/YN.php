<?php
namespace Df\Config\Source;
/**
 * 2019-07-06
 * The built-in @see \Magento\Config\Model\Config\Source\Yesno class can not be used
 * as `source_model` for an attribute, because it does not inherit from any class
 * and does not implement the `setAttribute` method,
 * so the @see \Magento\Eav\Model\Entity\Attribute\AbstractAttribute::getSource() method
 * fails with the error:
 * 		«Call to undefined method Magento\Config\Model\Config\Source\Yesno::setAttribute()»
 * https://github.com/magento/magento2/blob/2.3.2/app/code/Magento/Eav/Model/Entity/Attribute/AbstractAttribute.php#L652
 * That is why I implemented my own YesNo class which supports the `setAttribute` method,
 * because it has the @see \Magento\Framework\DataObject class as an ancestor.
 * @used-by \KingPalm\B2B\Setup\UpgradeData::_process()
 */
final class YN extends \Df\Config\SourceBase {
	/**
	 * 2019-07-06
	 * @override
	 * @see \Magento\Framework\Option\ArrayInterface::toOptionArray()
	 * @used-by \Magento\Config\Model\Config\Structure\Element\Field::_getOptionsFromSourceModel()
	 * @return array(array('label' => string, 'value' => int|string))
	 */
	function toOptionArray():array {return df_yes_no();}
}