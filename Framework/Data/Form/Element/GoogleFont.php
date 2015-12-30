<?php
namespace Df\Framework\Data\Form\Element;
class GoogleFont extends Select {
	/**
	 * 2015-11-28
	 * @override
	 * @see \Df\Framework\Data\Form\Hidden::onFormInitialized()
	 * @used-by \Df\Framework\Data\Form\Element\AbstractElementPlugin::afterSetForm()
	 * @return void
	 */
	public function onFormInitialized() {
		parent::onFormInitialized();
		$this->addClass('df-google-font');
		df_form_element_init($this, __CLASS__, 'Df_Core::lib/Select2/main.css', [
			'dataSource' => df_url_frontend('df-api/google/fonts')
			// 2015-12-07
			// Выбранное значение.
			,'value' => $this['value']
		]);
	}
}