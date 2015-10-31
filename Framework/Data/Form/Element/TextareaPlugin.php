<?php
namespace Df\Framework\Data\Form\Element;
use Magento\Framework\Data\Form\Element\Textarea;
class TextareaPlugin {
	/**
	 * 2015-10-27
	 * Цель метода — устранение дефекта
	 * «Class @see \Magento\Framework\Data\Form\Element\Textarea
	 * breaks specification of the parent class @see \Magento\Framework\Data\Form\Element\AbstractElement
	 * by not calling the method getBeforeElementHtml (getAfterElementHtml is called)»
	 * https://github.com/magento/magento2/issues/2202
	 * https://mage2.pro/t/150
	 * @see \Magento\Framework\Data\Form\Element\Textarea::getElementHtml()
	 * @param Textarea $subject
	 * @param string $result
	 * @return string
	 */
	public function afterGetElementHtml(Textarea $subject, $result) {
		/** @var string $before */
		$before = $subject->getBeforeElementHtml();
		if (!rm_starts_with($result, $before)) {
			$result = $before . $result;
		}
		return $result;
	}
}