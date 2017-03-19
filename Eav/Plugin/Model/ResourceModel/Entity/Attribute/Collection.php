<?php
namespace Df\Eav\Plugin\Model\ResourceModel\Entity\Attribute;
use Magento\Eav\Model\Entity\Attribute as A;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection as Sb;
final class Collection {
	/**
	 * 2015-08-29
	 * Цель метода — перевод экранных названий свойств товаров.
	 * @see \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection::addItem()
	 * @param Sb $sb
	 * @param A $item
	 * @return array(Attribute)
	 */
	function beforeAddItem(Sb $sb, A $item) {
		df_state()->attributeSet($item);
		try {$item['frontend_label'] = (string)__($item['frontend_label']);}
		finally {df_state()->attributeUnset();}
		return [$item];
	}
}