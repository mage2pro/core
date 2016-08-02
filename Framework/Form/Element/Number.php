<?php
namespace Df\Framework\Form\Element;
// 2016-08-02
class Number extends Text {
	/**
	 * 2016-08-02
	 * @override
	 * @see \Df\Framework\Form\Element\Text::onFormInitialized()
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterSetForm()
	 * @return void
	 */
	public function onFormInitialized() {
		parent::onFormInitialized();
		$this->addClass('df-number');
		df_fe_init($this, __CLASS__);
		$this->setAfterElementHtml($this[self::LABEL_RIGHT]);
	}

	/**
	 * 2016-08-02
	 * @used-by \Df\Framework\Form\Element\Number::onFormInitialized()
	 * @used-by \Dfe\AllPay\InstallmentSales\Plan\FormElement::onFormInitialized()
	 */
	const LABEL_RIGHT = 'label_right';
}