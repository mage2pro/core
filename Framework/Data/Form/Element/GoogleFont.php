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
		$this->addClass('df-google-font');
		df_form_element_init($this, 'googleFont/main',
			[
				'dataSource' => df_url_frontend('df-api/google/fonts')
				// 2015-12-07
				// Выбранное значение.
				,'value' => $this['value']
			]
			, [
				'Df_Core::lib/Select2/main.css'
				,'Df_Framework::formElement/googleFont/main.css'
			]
		);
	}

	/** @used-by \Df\Framework\Data\Form\Element\Fieldset\Font */
	const _C = __CLASS__;
}