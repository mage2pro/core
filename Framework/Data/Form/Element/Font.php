<?php
namespace Df\Framework\Data\Form\Element;
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
		$row1 = $this->inlineFieldset('df-checkboxes')->hide();
		$row1->checkbox('bold', 'B');
		$row1->checkbox('italic', 'I');
		$row1->checkbox('underline', 'U');
		$row1->color();
		//$this->field('family', GoogleFont::_C, 'Family')->setContainerClass('df-hidden');
		/** @var \Df\Framework\Data\Form\Element\Fieldset\Inline $row2 */
		$row2 = $this->inlineFieldset('df-family')->hide();
		$row2->field('family', GoogleFont::_C, 'Family');
		$row2->size();
		/** @var \Df\Framework\Data\Form\Element\Fieldset\Inline $row3 */
		$row3 = $this->inlineFieldset('row3')->hide();
		$row3->size('letter_spacing');
		/**
		 * 2015-12-13
		 * Намеренно используем в качестве аргумента $label пустую строку, а не null,
		 * потому что мы тем самым хотим сформировать пустой тег подписи <label><span></span></label>,
		 * чтобы затем прицепить к нему реальную подпись посредством FontAwesome.
		 */
		$row3->size('scale_horizontal', 'fa-text-width');
		$row3->size('scale_vertical', 'fa-text-height');
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