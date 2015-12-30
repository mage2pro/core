<?php
namespace Df\Framework\Data\Form\Element;
use Df\Framework\Data\Form\Element;
abstract class Table extends Hidden {
	/**
	 * 2015-12-16
	 * @used-by \Df\Framework\Data\Form\Element\Table::onFormInitialized()
	 * @return string[]
	 */
	abstract protected function columns();

	/**
	 * @override
	 * @see ElementI::onFormInitialized()
	 * @used-by \Df\Framework\Data\Form\Element\AbstractElementPlugin::afterSetForm()
	 * @return void
	 */
	public function onFormInitialized() {
		df_form_element_init($this, __CLASS__, 'Df_Core::lib/Handsontable/main.css', [
			'columns' => $this->columns()
		]);
	}
}