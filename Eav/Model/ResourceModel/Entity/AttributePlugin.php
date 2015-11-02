<?php
namespace Df\Eav\Model\ResourceModel\Entity;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
class AttributePlugin {
	/**
	 * 2015-09-30
	 * Цель метода — перевод экранных названий свойств (товаров, разделов, покупателей и т.п.).
	 * @see Attribute::load()
	 * @param Attribute $subject
	 * @param \Closure $proceed
	 * @param \Magento\Eav\Model\Entity\Attribute $object
	 * @param mixed $value
	 * @param string|null $field [optional]
	 * @return Attribute
	 */
	public function aroundLoad(
		Attribute $subject
		, \Closure $proceed
		, \Magento\Eav\Model\Entity\Attribute $object
		, $value
		, $field = null
	) {
		$proceed($object, $value, $field);
		df_state()->attributeSet($object);
		try {
			$object['frontend_label'] = (string)__($object['frontend_label']);
		}
		finally {
			df_state()->attributeUnset($object);
		}
		return $subject;
	}
}