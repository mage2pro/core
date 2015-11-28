<?php
use Magento\Framework\Data\Form\Element\AbstractElement;
/**
 * 2015-11-28
 * @param AbstractElement $element
 * @param string $widgetLocalPath
 * @param array(string => string) [optional]
 * @param string[] $css [optional]
 * @return void
 */
function df_form_element_init(AbstractElement $element, $widgetLocalPath, $params = [], $css = []) {
	/**
	 * 2015-11-23
	 * @used-by \Magento\Framework\Data\Form\Element\AbstractElement::getAfterElementHtml()
	 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Data/Form/Element/AbstractElement.php#L396-L404
	 * @used-by \Magento\Framework\Data\Form\Element\Fieldset::getElementHtml()
	 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Data/Form/Element/Fieldset.php#L53
	 */
	$element['after_element_html'] .= df_x_magento_init(
		'Df_Framework/js/formElement/' . $widgetLocalPath
		, ['id' => $element->getHtmlId()] + $params
	) . df_link_inline($css);
}

