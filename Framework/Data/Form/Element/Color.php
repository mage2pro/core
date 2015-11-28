<?php
namespace Df\Framework\Data\Form\Element;
class Color extends Text {
	/**
	 * 2015-11-24
	 * @override
	 * @see \Df\Framework\Data\Form\Element\Text::onFormInitialized()
	 * @used-by \Df\Framework\Data\Form\Element\AbstractElementPlugin::afterSetForm()
	 * @return void
	 */
	public function onFormInitialized() {
		$this->addClass('df-color');
		df_form_element_init($this, 'color');
	}

	/**
	 * 2015-11-24
	 * @used-by \Df\Framework\Data\Form\Element\Fieldset::color()
	 */
	const _C = __CLASS__;
}