<?php
namespace Df\Framework\Data\Form\Element;
class Color extends Text {
	/**
	 * 2015-11-24
	 * @override
	 * @see \Df\Framework\Data\Form\Element\Text::onFormInitialized()
	 * @used-by \Df\Framework\Data\Form\Element\AbstractElementPlugin::afterSetForm()
	 * @return void
	 */
	public function onFormInitialized() {
		/**
		 * 2015-11-23
		 * @used-by \Magento\Framework\Data\Form\Element\AbstractElement::getAfterElementHtml()
		 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Data/Form/Element/AbstractElement.php#L396-L404
		 * @used-by \Magento\Framework\Data\Form\Element\Fieldset::getElementHtml()
		 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Data/Form/Element/Fieldset.php#L53
		 */
		$this['after_element_html'] = df_x_magento_init('Df_Framework/js/form-element/color', [
			'id' => $this->getHtmlId()
		]);
		parent::onFormInitialized();
	}

	/**
	 * 2015-11-23
	 * @override
	 * @see \Magento\Framework\Data\Form\Element\Text::_construct()
	 * @used-by \Magento\Framework\Data\Form\AbstractForm::__construct()
	 * @return void
	 */
	protected function _construct() {
		$this->addClass('df-color');
		parent::_construct();
	}

	/**
	 * 2015-11-24
	 * @used-by \Df\Framework\Data\Form\Element\Fieldset::color()
	 */
	const _C = __CLASS__;
}