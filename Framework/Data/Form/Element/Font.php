<?php
namespace Df\Framework\Data\Form\Element;
use Df\Framework\Data\Form\Element\Renderer\Inline;
/**
 * Этот класс не является одиночкой:
 * https://github.com/magento/magento2/blob/2335247d4ae2dc1e0728ee73022b0a244ccd7f4c/lib/internal/Magento/Framework/Data/Form/AbstractForm.php#L155
 */
class Font extends Fieldset {
	/**
	 * 2015-11-17
	 * @override
	 * @see \Df\Framework\Data\Form\Element\Fieldset::_construct()
	 * @used-by \Magento\Framework\Data\Form\AbstractForm::__construct()
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
	}

	/**
	 * 2015-11-19
	 * @override
	 * @see \Df\Framework\Data\Form\Element\Fieldset::addSubElements()
	 * @used-by \Df\Framework\Data\Form\Element\Fieldset::setForm()
	 * @return void
	 */
	protected function addSubElements() {
		$this->yesNo('setup', 'Setup Appearance?');
		/** @var \Df\Framework\Data\Form\Element\Fieldset\Inline $fsCheckboxes */
		$fsCheckboxes = $this->inlineFieldset('df-checkboxes');
		$fsCheckboxes->checkbox('bold', 'Bold');
		$fsCheckboxes->checkbox('italic', 'Italic');
		$fsCheckboxes->checkbox('underline', 'Underline');
		/**
		 * 2015-11-18
		 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Data/Form/Element/Checkboxes.php#L83
		 */
		//$this->checkboxes(['bold' => 'Bold', 'italic' => 'Italic', 'underline' => 'Underline']);
		//$this->field('setup2', 'text', 'ТЕСТ');
		parent::addSubElements();
	}
}