<?php
namespace Df\Framework\Data\Form\Element;
use Magento\Framework\Data\Form\Element\AbstractElement;
class AbstractElementPlugin {
	/**
	 * 2015-10-09
	 * Цель метода — отключение автозаполнения полей.
	 * https://developers.google.com/web/fundamentals/input/form/label-and-name-inputs?hl=en#recommended-input-name-and-autocomplete-attribute-values
	 * @see \Magento\Framework\Data\Form\Element\AbstractElement::getHtmlAttributes()
	 * @param AbstractElement $subject
	 * @param string[] $result
	 * @return string[]
	 */
	public function afterGetHtmlAttributes(AbstractElement $subject, $result) {
		$result[]= 'autocomplete';
		return $result;
	}

	/**
	 * 2015-10-09
	 * Цель метода — отключение автозаполнения полей.
	 * https://developers.google.com/web/fundamentals/input/form/label-and-name-inputs?hl=en#recommended-input-name-and-autocomplete-attribute-values
	 * @see \Magento\Framework\Data\Form\Element\AbstractElement::getElementHtml()
	 * @param AbstractElement $subject
	 * @return array()
	 */
	public function beforeGetElementHtml(AbstractElement $subject) {
		$subject['autocomplete'] = 'new-password';
		return [];
	}
}
