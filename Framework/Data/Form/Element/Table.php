<?php
namespace Df\Framework\Data\Form\Element;
use Df\Framework\Data\Form\Element;
class Table extends Hidden {
	/**
	 * @override
	 * @see ElementI::onFormInitialized()
	 * @used-by \Df\Framework\Data\Form\Element\AbstractElementPlugin::afterSetForm()
	 * @return void
	 */
	public function onFormInitialized() {
		df_form_element_init($this, 'table/main', [
			'columns' => ['Column 1', 'Column 2', 'Column 3']
		], [
			'Df_Core::lib/Handsontable/main.css'
			,'Df_Framework::formElement/table/main.css'
		]);
	}
}