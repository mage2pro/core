<?php
namespace Df\Catalog\Block\Adminhtml\Category\Tab;
// 2016-02-17
// Класс \Magento\Catalog\Block\Adminhtml\Category\Tab\Attributes
// изменил имя в ветке develop, и мой код нужнается в переделке,
// чтобы поддерживать и ветку develop, и ветку production.
if (@class_exists('Magento\Catalog\Block\Adminhtml\Category\Tab\Attributes')) {
	class Attributes extends \Magento\Catalog\Block\Adminhtml\Category\Tab\Attributes {
		/**
		 * 2015-10-26
		 * @override
		 * Цель перекрытия — устранение дефекта https://github.com/magento/magento2/issues/2165
		 * Inconsistency:
		 * @see \Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Attributes::_getAdditionalElementTypes()
		 * fires the event «adminhtml_catalog_product_edit_element_types»
		 * but @see \Magento\Catalog\Block\Adminhtml\Category\Tab\Attributes::_getAdditionalElementTypes()
		 * does not fire a similar event.
		 * @see \Magento\Catalog\Block\Adminhtml\Category\Tab\Attributes::_getAdditionalElementTypes()
		 * Сделал по аналогии с
		 * @see \Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Attributes::_getAdditionalElementTypes()
		 * @return string[]
		 */
		protected function _getAdditionalElementTypes() {
			/** @var string[] $result */
			$result = parent::_getAdditionalElementTypes();
			$response = new \Magento\Framework\DataObject();
			$response['types'] = [];
			/**
			 * 2015-10-26
			 * Обработка этого события позволяет нам подставить свой класс
			 * для обработки свойств типа «textarea» раздела.
			 * вместо стандартного класса @see \Magento\Catalog\Block\Adminhtml\Helper\Form\Wysiwyg
			 */
			df_dispatch('adminhtml_catalog_category_edit_element_types', ['response' => $response]);
			foreach ($response['types'] as $typeName => $typeClass) {
				/** @var string $typeName */
				/** @var string $typeClass */
				$result[$typeName] = $typeClass;
			}
			return $result;
		}
	}
}
else {
	// 2016-02-17
	// Иначе будет:
	// [ReflectionException]
	// Class \Df\Catalog\Block\Adminhtml\Category\Tab\Attributes does not exist
	class Attributes {}
}
