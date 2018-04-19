<?php
namespace Df\Framework\Form\Element;
/**
 * 2016-08-02
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 * @used-by \Df\Framework\Form\Element\Fieldset::number()
 */
class Number extends Text {
	/**
	 * 2016-08-02
	 * @override
	 * @see \Df\Framework\Form\Element\Text::onFormInitialized()
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterSetForm()
	 */
	final function onFormInitialized() {
		parent::onFormInitialized();
		$this->addClass('df-number');
		df_fe_init($this, __CLASS__);
		$this->setAfterElementHtml($this[self::LABEL_RIGHT]);
	}

	/**
	 * 2016-08-02
	 * @used-by \Df\Framework\Form\Element\Number::onFormInitialized()
	 * @used-by \Dfe\AllPay\InstallmentSales\Plan\FE::onFormInitialized()
	 */
	const LABEL_RIGHT = 'label_right';
}