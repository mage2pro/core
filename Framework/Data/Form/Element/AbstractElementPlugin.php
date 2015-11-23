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
	 * 2015-11-24
	 * Многие операции над элементом допустимы только при наличии формы,
	 * поэтому мы выполняем их в обработчике @see \Df\Framework\Data\Form\Element::onFormInitialized
	 * @see \Magento\Framework\Data\Form\Element\AbstractElement::setForm()
	 * @param AbstractElement $subject
	 * @param AbstractElement $result
	 * @return string[]
	 */
	public function afterSetForm(AbstractElement $subject, AbstractElement $result) {
		if (!isset($subject->{__METHOD__}) && $subject instanceof \Df\Framework\Data\Form\ElementI) {
			$subject->onFormInitialized();
			$subject->{__METHOD__} = true;
		}
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
