<?php
namespace Df\Framework\Form\Element;
use Df\Framework\Form\Element as E;
use Magento\Framework\Phrase;
// 2016-07-30
class Quantity extends Fieldset\Inline {
	/**
	 * 2016-07-30
	 * @override
	 * @see \Df\Framework\Form\Element\Text::onFormInitialized()
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterSetForm()
	 */
	function onFormInitialized() {
		parent::onFormInitialized();
		$this->addClass('df-quantity');
		$title = $this->getTitle(); /** @var string|null|Phrase $title */
		$this->unsTitle();
		$input = $this->text('value', $this->getLabel(), ['title' => $title]); /** @var Text|E $input */
		$this->unsLabel();
		$values = dfa($this->_data, self::P__VALUES, []); /** @var array(int|string => string)|string $values */
		if (is_string($values)) {
			$values = [$values];
		}
		$this->unsetData(self::P__VALUES);
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