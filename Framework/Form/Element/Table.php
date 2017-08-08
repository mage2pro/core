<?php
namespace Df\Framework\Form\Element;
/**
 * 2015-12-16
 * @see \Dfe\Typography\Config\Fonts
 */
abstract class Table extends Hidden {
	/**
	 * 2015-12-16
	 * @used-by \Df\Framework\Form\Element\Table::onFormInitialized()
	 * @see \Dfe\Typography\Config\Fonts::columns()
	 * @return string[]
	 */
	abstract protected function columns();

	/**
	 * 2015-12-16
	 * @override
	 * @see \Df\Framework\Form\Element\Hidden::onFormInitialized()
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterSetForm()
	 */
	final function onFormInitialized() {
		df_fe_init($this, __CLASS__, df_asset_third_party('Handsontable/main.css'), [
			'columns' => $this->columns()
		])
	;}
}