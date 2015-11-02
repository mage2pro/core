<?php
namespace Df\Eav\Model\ResourceModel\Entity\Attribute;
use \Magento\Eav\Model\Entity\Attribute;
use \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection;
class CollectionPlugin {
	/**
	 * 2015-08-29
	 * Цель метода — перевод экранных названий свойств товаров.
	 * @param Collection $subject
	 * @param Attribute $item
	 * @return array(Attribute)
	 */
	public function beforeAddItem(Collection $subject, Attribute $item) {
		df_state()->attributeSet($item);
		try {
			$item['frontend_label'] = (string)__($item['frontend_label']);
		}
		finally {
			df_state()->attributeUnset();
		}
		return [$item];
	}
}