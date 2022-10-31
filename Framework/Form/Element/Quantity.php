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
	final function onFormInitialized():void {
		parent::onFormInitialized();
		$this->addClass('df-quantity');
		$t = $this->getTitle(); /** @var string|null|Phrase $t */
		$this->unsTitle();
		$input = $this->text('value', $this->getLabel(), ['title' => $t]); /** @var Text|E $input */
		$this->unsLabel();
		if (is_string($v = dfa($this->_data, self::P__VALUES, []))) { /** @var array(int|string => string)|string $v */
			$v = [$v];
		}
		$this->unsetData(self::P__VALUES);
		1 < count($v) ? $this->select('units', null, $v, ['title' => $t]) : $input->setAfterElementHtml(df_first($v));
		df_fe_init($this, __CLASS__);
	}

	/**
	 * 2016-07-30
	 * @used-by self::onFormInitialized()
	 * @used-by \Df\Framework\Form\Element\Fieldset::size()
	 * @used-by \Df\Framework\Form\Element\Font::onFormInitialized()
	 */
	const P__VALUES = 'values';
}