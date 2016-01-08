<?php
namespace Df\Framework\Plugin\Data\Form\Element;
use Magento\Framework\Data\Form\Element\Textarea as Sb;
class Textarea {
	/**
	 * 2015-10-27
	 * Цель метода — устранение дефекта
	 * «Class @see \Magento\Framework\Data\Form\Element\Textarea
	 * breaks specification of the parent class @see \Magento\Framework\Data\Form\Element\AbstractElement
	 * by not calling the method getBeforeElementHtml (getAfterElementHtml is called)»
	 * https://github.com/magento/magento2/issues/2202
	 * https://mage2.pro/t/150
	 * @see \Magento\Framework\Data\Form\Element\Textarea::getElementHtml()
	 * @param Sb $sb
	 * @param string $result
	 * @return string
	 */
	public function afterGetElementHtml(Sb $sb, $result) {
		/** @var string $before */
		$before = $sb->getBeforeElementHtml();
		return (!df_starts_with($result, $before) ? $before : '') . $result;
	}
}