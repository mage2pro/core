<?php
namespace Df\Eav\Plugin\Model\ResourceModel\Entity;
use Magento\Eav\Model\Entity\Attribute as A;
use Magento\Eav\Model\ResourceModel\Entity\Attribute as Sb;
final class Attribute {
	/**
	 * 2015-09-30
	 * Цель метода — перевод экранных названий свойств (товаров, разделов, покупателей и т.п.).
	 * @see \Magento\Eav\Model\ResourceModel\Entity\Attribute::load()
	 * @param Sb $sb
	 * @param \Closure $f
	 * @param A $a
	 * @param mixed $value
	 * @param string|null $field [optional]
	 * @return Sb
	 */
	function aroundLoad(Sb $sb, \Closure $f, A $a, $value, $field = null) {
		$f($a, $value, $field);
		df_state()->attributeSet($a);
		try {$a['frontend_label'] = (string)__($a['frontend_label']);}
		finally {df_state()->attributeUnset($a);}
		return $sb;
	}
}