<?php
namespace Df\Eav\Model\Entity\Attribute\Frontend;
use Magento\Eav\Model\Entity\Attribute\Frontend\AbstractFrontend;
class AbstractFrontendPlugin {
	/**
	 * 2015-09-20
	 * Цель метода — перевод экранных названий свойств (товаров, разделов, покупателей и т.п.).
	 * @see \Df\Eav\Model\Entity\AttributePlugin::aroundGetStoreLabels()
	 * @see \Df\Framework\Data\Form\Element\AbstractElementPlugin::afterGetEscapedValue()
	 * @see AbstractFrontend::getLabel()
	 * @param AbstractFrontend $subject
	 * @param string $result
	 * @return string
	 */
	public function afterGetLabel(AbstractFrontend $subject, $result) {
		df_state()->attributeSet($subject->getAttribute());
		/** @var string[] $result */
		try {
			/**
			 * 2015-09-21
			 * Важно сразу привести результат к строке,
			 * потому что иначе @see __() вернёт объект и отложит перевод на потом,
			 * когда мы уже выпадем из конекста свойства (finally ниже).
			 */
			$result = (string)__($result);
		}
		finally {
			df_state()->attributeUnset();
		}
		return $result;
	}
}