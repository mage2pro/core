<?php
namespace Df\Framework\Data\Form\Element\Fieldset;
use Df\Framework\Data\Form\Element\Fieldset;
/**
 * Этот класс не является одиночкой:
 * https://github.com/magento/magento2/blob/2335247d4ae2dc1e0728ee73022b0a244ccd7f4c/lib/internal/Magento/Framework/Data/Form/AbstractForm.php#L155
 */
class Font extends Fieldset {
	/**
	 * 2015-11-19
	 * @override
	 * @see \Df\Framework\Data\Form\Element\Fieldset::onFormInitialized()
	 * @used-by \Df\Framework\Data\Form\Element\AbstractElementPlugin::afterSetForm()
	 * @return void
	 */
	public function onFormInitialized() {
		$this->checkbox('setup', 'Setup?')->addClass('df-setup');
		/** @var \Df\Framework\Data\Form\Element\Fieldset\Inline $fsCheckboxes */
		$fsCheckboxes = $this->inlineFieldset('df-checkboxes')->addClass('df-checkbox')->hide();
		$fsCheckboxes->checkbox('bold', 'B');//->setLabelPosition(ElementI::BEFORE);
		$fsCheckboxes->checkbox('italic', 'I');//->setLabelPosition(ElementI::BEFORE);
		$fsCheckboxes->checkbox('underline', 'U');//->setLabelPosition(ElementI::BEFORE);
		//$fsCheckboxes->checkbox('bold2', '')->setContainerClass('df-checkbox-aw');
		$fsCheckboxes->color();
		$this->select('letter_case', 'Letter Case', \Df\Config\Source\LetterCase::s())
			->addClass('df-letter-case')
			->setContainerClass('df-hidden')
		;
		$this->select('family', 'Family', \Df\Config\Source\GoogleFont::s())
			->addClass('df-family')
			->setContainerClass('df-hidden')
		;
//		$this->color();
		/**
		 * 2015-11-23
		 * @used-by \Magento\Framework\Data\Form\Element\AbstractElement::getAfterElementHtml()
		 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Data/Form/Element/AbstractElement.php#L396-L404
		 * @used-by \Magento\Framework\Data\Form\Element\Fieldset::getElementHtml()
		 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Data/Form/Element/Fieldset.php#L53
		 */
		$this['after_element_html'] = df_x_magento_init('Df_Framework/js/form-element/font', [
			'id' => $this->getHtmlId()
		]);
		parent::onFormInitialized();
	}

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
}