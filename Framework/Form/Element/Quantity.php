<?php
namespace Df\Framework\Form\Element;
use Df\Framework\Form\Element as E;
use Magento\Framework\Phrase;
class Quantity extends Fieldset\Inline {
	/**
	 * 2016-07-30
	 * @override
	 * @see \Df\Framework\Form\Element\Text::onFormInitialized()
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterSetForm()
	 * @return void
	 */
	public function onFormInitialized() {
		parent::onFormInitialized();
		$this->addClass('df-quantity');
		/** @var string|null|Phrase $title */
		$title = $this->getTitle();
		$this->unsTitle();
		/** @var Text|E $input */
		$input = $this->text('value', $this->getLabel(), ['title' => $title]);
		$this->unsLabel();
		/** @var array(int|string => string)|string $values */
		$values = dfa($this->_data, self::P__VALUES, []);
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