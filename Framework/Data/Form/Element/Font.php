<?php
namespace Df\Framework\Data\Form\Element;
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
		$this->yesNo('setup', 'Setup Appearance?');
		$this->checkbox('bold', 'Bold');
		$this->checkbox('italic', 'Italic');
		$this->checkbox('underline', 'Underline');
		//$this->field('setup2', 'text', 'ТЕСТ');
		parent::_construct();
	}
}