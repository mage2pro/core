<?php
use Df\Core\Model\Format\Html;
/**
 * @param string $class
 * @param string|null $content
 * @return string
 */
function rm_div($class, $content = null) {return rm_tag('div', array('class' => $class), $content);}

/**
 * @used-by rm_html_select_yesno()
 * @used-by Df_Admin_Block_Column_Select::renderHtml()
 * @used-by Df_Checkout_Block_Frontend_Ergonomic_Address_Field_Country::getDropdownAsHtml()
 * @param array(int|string => string)|array(array(string => int|string|mixed[])) $options
 * @param string|null $selected [optional]
 * @param array(string => string) $attributes [optional]
 * @return string
 */
function rm_html_select(array $options, $selected = null, array $attributes = array()) {
	return Html\Select::render($options, $selected, $attributes);
}

/**
 * @used-by app/design/adminhtml/rm/default/template/df/access_control/tab.phtml
 * @param bool|null $selected [optional]
 * @param array(string => string) $attributes [optional]
 * @return string
 */
function rm_html_select_yesno($selected = null, array $attributes = array()) {
	return rm_html_select(array('нет', 'да'), is_null($selected) ? null : (int)$selected, $attributes);
}

/**
 * 2015-04-16
 * Отныне значением атрибута может быть массив:
 * @see Df_Core_Model_Format_Html_Tag::getAttributeAsText()
 * Передавать в качестве значения массив имеет смысл, например, для атрибута «class».
 * @used-by rm_div()
 * @param string $tag
 * @param array(string => string|string[]|int|null) $attributes [optional]
 * @param string $content [optional]
 * @return string
 */
function rm_tag($tag, array $attributes = array(), $content = null) {
	return Html\Tag::render($tag, $attributes, $content);
}

/**
 * @param string[] $items
 * @param bool $isOrdered [optional]
 * @param string|null $cssClassForList [optional]
 * @param string|null $cssClassForItem [optional]
 * @return string
 */
function rm_tag_list(
	array $items, $isOrdered = false, $cssClassForList = null, $cssClassForItem = null
) {
	return Html\ListT::render($items, $isOrdered, $cssClassForList, $cssClassForItem);
}


