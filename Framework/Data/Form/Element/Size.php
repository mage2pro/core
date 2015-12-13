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
		$this->unsLabel();
		$this->unsTitle();
		/** @var array(int|string => string)|string|\Df\Config\Source\SizeUnit $values */
		$values = df_a($this->_data, self::P__VALUES, \Df\Config\Source\SizeUnit::s());
		if (is_string($values)) {
			$values = [$values];
		}
		unset($this->_data[self::P__VALUES]);
		$this->select('units', null, $values);
		df_form_element_init($this, null, [], 'Df_Framework::formElement/size/main.css');
	}

	/**
	 * 2015-12-11
	 * @used-by \Df\Framework\Data\Form\Element\Fieldset::size()
	 */
	const _C = __CLASS__;
	const P__VALUES = 'values';
}