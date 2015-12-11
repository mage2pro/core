<?php
namespace Df\Framework\Data\Form\Element;
class Size extends Text {
	/**
	 * 2015-11-24
	 * @override
	 * @see \Df\Framework\Data\Form\Element\Text::onFormInitialized()
	 * @used-by \Df\Framework\Data\Form\Element\AbstractElementPlugin::afterSetForm()
	 * @return void
	 */
	public function onFormInitialized() {
		$this->addClass('df-size');
		df_form_element_init($this, null, [], 'Df_Framework::formElement/size/main.css');
	}

	/**
	 * 2015-12-11
	 * @used-by \Df\Framework\Data\Form\Element\Fieldset::size()
	 */
	const _C = __CLASS__;
}