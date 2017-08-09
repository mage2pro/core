<?php
namespace Df\Framework\Form\Element;
use Df\Framework\Form\Element as E;
use Magento\Framework\Phrase;
/**
 * 2016-07-30
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 * @used-by \Df\Framework\Form\Element\Fieldset::quantity()
 */
class Quantity extends Fieldset\Inline {
	/**
	 * 2016-07-30
	 * @override
	 * @see \Df\Framework\Form\Element\Text::onFormInitialized()
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterSetForm()
	 */
	final function onFormInitialized() {
		parent::onFormInitialized();
		$this->addClass('df-quantity');
		$title = $this->getTitle(); /** @var string|null|Phrase $title */
		$this->unsTitle();
		$input = $this->text('value', $this->getLabel(), ['title' => $title]); /** @var Text|E $input */
		$this->unsLabel();
		/** @var array(int|string => string)|string $values */
		if (is_string($values = dfa($this->_data, self::P__VALUES, []))) {
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

	/**
	 * 2016-07-30
	 * @used-by onFormInitialized()
	 * @used-by \Df\Framework\Form\Element\Fieldset::size()
	 * @used-by \Df\Framework\Form\Element\Font::onFormInitialized()
	 */
	const P__VALUES = 'values';
}