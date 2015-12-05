<?php
namespace Df\Framework\Data\Form\Element\Fieldset;
use Df\Framework\Data\Form\Element\Fieldset;
use Df\Framework\Data\Form\Element\GoogleFont;
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
		parent::onFormInitialized();
		$this->addClass('df-font');
		$this->checkbox('setup', 'Setup?')->addClass('df-setup');
		/** @var \Df\Framework\Data\Form\Element\Fieldset\Inline $fsCheckboxes */
		$fsCheckboxes = $this->inlineFieldset('fieldset', 'df-checkboxes')->addClass('df-checkbox')->hide();
		$fsCheckboxes->checkbox('bold', 'B');//->setLabelPosition(ElementI::BEFORE);
		$fsCheckboxes->checkbox('italic', 'I');//->setLabelPosition(ElementI::BEFORE);
		$fsCheckboxes->checkbox('underline', 'U');//->setLabelPosition(ElementI::BEFORE);
		//$fsCheckboxes->checkbox('bold2', '')->setContainerClass('df-checkbox-aw');
		$fsCheckboxes->color();
		$this->select('letter_case', 'Letter Case', \Df\Config\Source\LetterCase::s())
			->addClass('df-letter-case')
			->setContainerClass('df-hidden')
		;
		$this->field('family', GoogleFont::_C, 'Family')
			->addClass('df-family')
			->setContainerClass('df-hidden')
		;
//		$this->color();
		df_form_element_init($this, 'font/main', [], [
			'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.css'
			,'Df_Framework::formElement/font/main.css'
		], 'before');
	}
}