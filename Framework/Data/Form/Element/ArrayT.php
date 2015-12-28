<?php
namespace Df\Framework\Data\Form\Element;
/**
 * Этот класс не является одиночкой:
 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Data/Form/AbstractForm.php#L155
 */
class ArrayT extends Fieldset {
	/**
	 * 2015-11-19
	 * @override
	 * @see \Df\Framework\Data\Form\Element\Fieldset::onFormInitialized()
	 * @used-by \Df\Framework\Data\Form\Element\AbstractElementPlugin::afterSetForm()
	 * @return void
	 */
	public function onFormInitialized() {
		parent::onFormInitialized();
		$this->addClass('df-array');
		df_form_element_init($this, 'array/main', [], [
			'Df_Framework::formElement/array/main.css'
		], 'before');
	}
}