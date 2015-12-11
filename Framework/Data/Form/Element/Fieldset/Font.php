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
		/** @var \Df\Framework\Data\Form\Element\Fieldset\Inline $row1 */
		$row1 = $this->inlineFieldset('row1', 'df-checkboxes')->hide();
		$row1->checkbox('bold', 'B');
		$row1->checkbox('italic', 'I');
		$row1->checkbox('underline', 'U');
		$row1->color();
		//$this->field('family', GoogleFont::_C, 'Family')->setContainerClass('df-hidden');
		/** @var \Df\Framework\Data\Form\Element\Fieldset\Inline $row2 */
		$row2 = $this->inlineFieldset('row2', 'df-family')->hide();
		$row2->field('family', GoogleFont::_C, 'Family');
		$row2->size();
		$this->select('letter_case', 'Letter Case', \Df\Config\Source\LetterCase::s())
			->addClass('df-letter-case')
			->setContainerClass('df-hidden')
		;
		df_form_element_init($this, 'font/main', [], [
			'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.css'
			,'Df_Framework::formElement/font/main.css'
		], 'before');
	}
}