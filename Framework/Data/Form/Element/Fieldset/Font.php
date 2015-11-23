<?php
namespace Df\Framework\Data\Form\Element\Fieldset;
use Df\Framework\Data\Form\Element\AbstractElement as DfAbstractElement;
use Df\Framework\Data\Form\Element\Fieldset;
/**
 * Этот класс не является одиночкой:
 * https://github.com/magento/magento2/blob/2335247d4ae2dc1e0728ee73022b0a244ccd7f4c/lib/internal/Magento/Framework/Data/Form/AbstractForm.php#L155
 */
class Font extends Fieldset {
	/**
	 * 2015-11-19
	 * @override
	 * @see \Df\Framework\Data\Form\Element\Fieldset::_construct()
	 * @used-by \Magento\Framework\Data\Form\AbstractForm::__construct()
	 * @return void
	 */
	protected function _construct() {
		$this->addClass('df-font');
		parent::_construct();
	}

	/**
	 * 2015-11-19
	 * @override
	 * @see \Df\Framework\Data\Form\Element\Fieldset::onFormInitialized()
	 * @used-by \Df\Framework\Data\Form\Element\Fieldset::setForm()
	 * @return void
	 */
	protected function onFormInitialized() {
		$this->checkbox('setup', 'Setup?')->addClass('df-setup');
		/** @var \Df\Framework\Data\Form\Element\Fieldset\Inline $fsCheckboxes */
		$fsCheckboxes = $this->inlineFieldset('df-checkboxes')->addClass('df-checkbox')->hide();
		$fsCheckboxes->checkbox('bold', 'Bold');
		$fsCheckboxes->checkbox('italic', 'Italic');
		$fsCheckboxes->checkbox('underline', 'Underline');
		$this->select('letter_case', 'Letter Case', \Df\Config\Source\LetterCase::s())
			->addClass('df-letter-case')
			->setData(DfAbstractElement::CONTAINER_CLASS, 'df-hidden')
		;
		/**
		 * 2015-11-18
		 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Data/Form/Element/Checkboxes.php#L83
		 */
		//$this->checkboxes(['bold' => 'Bold', 'italic' => 'Italic', 'underline' => 'Underline']);
		//$this->field('setup2', 'text', 'ТЕСТ');
		/**
		 * 2015-11-23
		 * @used-by \Magento\Framework\Data\Form\Element\AbstractElement::getAfterElementHtml()
		 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Data/Form/Element/AbstractElement.php#L396-L404
		 * @used-by \Magento\Framework\Data\Form\Element\Fieldset::getElementHtml()
		 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Data/Form/Element/Fieldset.php#L53
		 */
		$this['after_element_html'] = df_x_magento_init('Df_Framework/form-element/font', [
			'id' => $this->getHtmlId()
		]);
		parent::onFormInitialized();
	}
}