<?php
use Df\Framework\Data\Form\Element;
use Magento\Framework\Data\Form\Element\AbstractElement;
/**
 * 2015-11-28
 * @param AbstractElement $element
 * @param string|null $widgetLocalPath [optional]
 * @param array(string => string) [optional]
 * @param string|string[] $css [optional]
 * @param string $position [optional]
 * @return void
 */
function df_form_element_init(
	AbstractElement $element, $widgetLocalPath = null, $params = [], $css = [], $position = 'after') {
	/**
	 * 2015-11-23
	 * @used-by \Magento\Framework\Data\Form\Element\AbstractElement::getAfterElementHtml()
	 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Data/Form/Element/AbstractElement.php#L396-L404
	 * @used-by \Magento\Framework\Data\Form\Element\Fieldset::getElementHtml()
	 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Data/Form/Element/Fieldset.php#L53
	 */
	/** @var string $contents */
	$contents = '';
	if ($widgetLocalPath) {
		$contents .= df_x_magento_init(
			'Df_Framework/formElement/' . $widgetLocalPath
			, ['id' => $element->getHtmlId()] + $params
		);
	}
	if ($css) {
		$contents .= df_link_inline($css);
	}
	$element["{$position}_element_html"] .= $contents;
}

/**
 * 2015-12-14
 * @param AbstractElement|Element $element
 * @return void
 */
function df_hide(AbstractElement $element) {$element->setContainerClass('df-hidden');}

