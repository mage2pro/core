<?php
namespace Df\Framework\Form\Element;
// 2015-11-24
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Color extends Text {
	/**
	 * 2015-11-24
	 * @override
	 * @see \Df\Framework\Form\Element\Text::onFormInitialized()
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterSetForm()
	 */
	final function onFormInitialized() {
		parent::onFormInitialized();
		$this->addClass('df-color');
		df_fe_init($this, __CLASS__, df_asset_third_party('ColorPicker/main.css'));
	}
}