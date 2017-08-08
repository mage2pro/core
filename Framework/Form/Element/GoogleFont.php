<?php
namespace Df\Framework\Form\Element;
// 2015-11-28
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class GoogleFont extends Select {
	/**
	 * 2015-11-28
	 * @override
	 * @see \Df\Framework\Form\Hidden::onFormInitialized()
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterSetForm()
	 */
	final function onFormInitialized() {
		parent::onFormInitialized();
		$this->addClass('df-google-font');
		df_fe_init($this, __CLASS__, df_asset_third_party('Select2/main.css'), [
			'dataSource' => df_url_frontend('df-google-font')
			,'value' => $this['value'] // 2015-12-07 It is the selected value.
		]);
	}
}