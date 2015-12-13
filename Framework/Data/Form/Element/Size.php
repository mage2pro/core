<?php
namespace Df\Framework\Data\Form\Element;
class Size extends Fieldset\Inline {
	/**
	 * 2015-11-24
	 * @override
	 * @see \Df\Framework\Data\Form\Element\Text::onFormInitialized()
	 * @used-by \Df\Framework\Data\Form\Element\AbstractElementPlugin::afterSetForm()
	 * @return void
	 */
	public function onFormInitialized() {
		parent::onFormInitialized();
		$this->addClass('df-size');
		$this->text('value', $this->getLabel());
		$this->unsetLabel();
		$this->unsetTitle();
		$this->select('units', null, \Df\Config\Source\SizeUnit::s());
		df_form_element_init($this, null, [], 'Df_Framework::formElement/size/main.css');
	}

	/**
	 * 2015-12-11
	 * @used-by \Df\Framework\Data\Form\Element\Fieldset::size()
	 */
	const _C = __CLASS__;
}