<?php
namespace Df\Eav\Plugin\Model\ResourceModel\Entity;
use Magento\Eav\Model\Entity\Attribute as A;
use Magento\Eav\Model\ResourceModel\Entity\Attribute as Sb;
class Attribute {
	/**
	 * 2015-09-30
	 * Цель метода — перевод экранных названий свойств (товаров, разделов, покупателей и т.п.).
	 * @see \Magento\Eav\Model\ResourceModel\Entity\Attribute::load()
	 * @param Sb $sb
	 * @param \Closure $proceed
	 * @param A $object
	 * @param mixed $value
	 * @param string|null $field [optional]
	 * @return Sb
	 */
	function aroundLoad(Sb $sb, \Closure $proceed, A $object, $value, $field = null) {
		$proceed($object, $value, $field);
		df_state()->attributeSet($object);
		try {$object['frontend_label'] = (string)__($object['frontend_label']);}
		finally {df_state()->attributeUnset($object);}
		return $sb;
	}
}