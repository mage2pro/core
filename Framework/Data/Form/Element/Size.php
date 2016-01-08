<?php
namespace Df\Framework\Data\Form\Element;
use Df\Config\Source\SizeUnit;
use Df\Framework\Data\Form\Element as E;
use Magento\Framework\Phrase;
class Size extends Fieldset\Inline {
	/**
	 * 2015-11-24
	 * @override
	 * @see \Df\Framework\Data\Form\Element\Text::onFormInitialized()
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterSetForm()
	 * @return void
	 */
	public function onFormInitialized() {
		parent::onFormInitialized();
		$this->addClass('df-size');
		/** @var string|null|Phrase $title */
		$title = $this->getTitle();
		$this->unsTitle();
		/** @var Text|E $input */
		$input = $this->text('value', $this->getLabel(), ['title' => $title]);
		$this->unsLabel();
		/** @var array(int|string => string)|string $values */
		$values = df_a($this->_data, self::P__VALUES, SizeUnit::s()->toOptionArray());
		if (is_string($values)) {
			$values = [$values];
		}
		unset($this->_data[self::P__VALUES]);
		if (1 < count($values)) {
			$this->select('units', null, $values, ['title' => $title]);
		}
		else {
			$input->setAfterElementHtml(df_first($values));
		}
		df_fe_init($this, __CLASS__);
	}
	const P__VALUES = 'values';
}