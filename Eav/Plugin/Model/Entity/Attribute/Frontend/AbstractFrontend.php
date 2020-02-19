<?php
namespace Df\Eav\Plugin\Model\Entity\Attribute\Frontend;
use Magento\Eav\Model\Entity\Attribute\Frontend\AbstractFrontend as Sb;
final class AbstractFrontend {
	/**
	 * 2015-09-20
	 * Цель метода — перевод экранных названий свойств (товаров, разделов, покупателей и т.п.).
	 * @see \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterGetEscapedValue()
	 * @see \Magento\Eav\Model\Entity\Attribute\Frontend\AbstractFrontend::getLabel()
	 * @param Sb $sb
	 * @param string $r
	 * @return string
	 */
	function afterGetLabel(Sb $sb, $r) {
		df_state()->attributeSet($sb->getAttribute());
		/**
		 * 2015-09-21
		 * Важно сразу привести результат к строке,
		 * потому что иначе @see __() вернёт объект и отложит перевод на потом,
		 * когда мы уже выпадем из контекста свойства (finally ниже).
		 */
		try {$r = (string)__($r);}
		finally {df_state()->attributeUnset();}
		return $r;
	}
}