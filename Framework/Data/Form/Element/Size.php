<?php
namespace Df\Framework\Data\Form\Element;
use Df\Framework\Data\Form\Element;
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
		/** @var Text|Element $input */
		$input = $this->text('value', $this->getLabel());
		$this->unsLabel();
		$this->unsTitle();
		/** @var array(int|string => string)|string $values */
		$values = df_a($this->_data, self::P__VALUES, \Df\Config\Source\SizeUnit::s()->toOptionArray());
		if (is_string($values)) {
			$values = [$values];
		}
		unset($this->_data[self::P__VALUES]);
		if (1 < count($values)) {
			$this->select('units', null, $values);
		}
		else {
			$input->setAfterElementHtml(df_first($values));
		}
		df_form_element_init($this, null, [], 'Df_Framework::formElement/size/main.css');
	}

	/**
	 * 2015-12-11
	 * @used-by \Df\Framework\Data\Form\Element\Fieldset::size()
	 */
	const _C = __CLASS__;
	const P__VALUES = 'values';
}