<?php
use Df\Core\Format\Html;
/**
 * @param string $class
 * @param string|array(string => mixed)|null $content
 * @return string
 */
function df_div($class, $content = null) {return df_tag('div', $class, $content);}

/**
 * 2016-11-13
 * @param string[] $args
 * @return string|string[]
 */
function df_html_b(...$args) {return df_call_a(function($s) {return df_tag('b', [], $s);}, $args);}

/**
 * @used-by df_html_select_yesno()
 * @used-by Df_Admin_Block_Column_Select::renderHtml()
 * @used-by Df_Checkout_Block_Frontend_Ergonomic_Address_Field_Country::getDropdownAsHtml()
 * @param array(int|string => string)|array(array(string => int|string|mixed[])) $options
 * @param string|null $selected [optional]
 * @param array(string => string) $attributes [optional]
 * @return string
 */
function df_html_select(array $options, $selected = null, array $attributes = []) {return
	Html\Select::render($options, $selected, $attributes)
;}

/**
 * @used-by app/design/adminhtml/rm/default/template/df/access_control/tab.phtml
 * @param bool|null $selected [optional]
 * @param array(string => string) $attributes [optional]
 * @return string
 */
function df_html_select_yesno($selected = null, array $attributes = []) {return df_html_select(
	['нет', 'да'], is_null($selected) ? null : (int)$selected, $attributes
);}

/**
 * 2015-10-27
 * @used-by df_fa_link()
 * @used-by df_fe_init()
 * @used-by \Df\Phone\Js::_toHtml()
 * @used-by \Dfe\Customer\Block::_toHtml()
 * @used-by \Dfe\Frontend\Block\ProductView\Css::_toHtml()
 * @used-by \Dfe\Klarna\Button::_toHtml()
 * @used-by \Dfe\Markdown\FormElement::css()
 * @used-by \Dfe\Stripe\Block\Multishipping::_toHtml()
 * @param string[] $args
 * @return string
 */
function df_link_inline(...$args) {return df_call_a(function($res) {return df_resource_inline(
	$res, function($url) {return df_tag(
		'link', ['href' => $url, 'rel' => 'stylesheet', 'type' => 'text/css'], null, false
	);}
);}, $args);}

/**
 * 2015-12-11
 * Применяем кэширование, чтобы не загружать повторно один и тот же файл CSS.
 * Как оказалось, браузер при наличии на странице нескольких тегов link с одинаковым адресом
 * применяет одни и те же правила несколько раз (хотя, видимо, не делает повторных обращений к серверу
 * при включенном в браузере кэшировании браузерных ресурсов).
 * 2016-03-23
 * Добавил обработку пустой строки $resource.
 * Нам это нужно, потому что пустую строку может вернуть @see \Df\Typography\Font::link()
 * https://mage2.pro/t/1010
 * @used-by df_js_inline()
 * @used-by df_link_inline()
 * @param string $r
 * @param \Closure $f
 * @return string
 */
function df_resource_inline($r, \Closure $f) {
	/** @var array(string => bool) $c */ static $c;
	if (!$r || isset($c[$r])) {$result = '';}
	else {$c[$r] = true; $result = $f(df_asset_create($r)->getUrl());}
	return $result;
}

/**
 * 2015-12-21
 * 2015-12-25: Пустой тег style приводит к белому экрану в Chrome: <style type='text/css'/>.
 * @used-by df_style_inline_hide()
 * @used-by \Df\Sso\Button::loggedOut()
 * @used-by \Dfe\Frontend\Block\ProductView\Css::_toHtml()
 * @param string $css
 * @return string
 */
function df_style_inline($css) {return !$css ? '' : df_tag('style', ['type' => 'text/css'], $css);}

/**
 * 2016-12-04
 * @used-by \Df\Sso\Css::_toHtml()
 * @used-by \Df\Sso\Css::_toHtml()
 * @used-by \Frugue\Shipping\Header::_toHtml()
 * @param string[] $selectors
 * @return string
 */
function df_style_inline_hide(...$selectors) {return !$selectors ? '' : df_style_inline(
	df_csv_pretty($selectors) . ' {display: none !important;}'
);}

/**
 * 2015-04-16
 * Отныне значением атрибута может быть массив:
 * @see \Df\Core\Format\Html\Tag::getAttributeAsText()
 * Передавать в качестве значения массив имеет смысл, например, для атрибута «class».
 *
 * 2016-05-30
 * Отныне в качестве параметра $attributes можно передавать строку вместо массива.
 * В этом случае значение $attributes считается классом CSS формируемого элемента.
 *
 * @used-by df_div()
 * @used-by df_js()
 * @used-by df_js_data()
 * @used-by \AlbumEnvy\Popup\Content::_toHtml()
 * @used-by \Df\Config\Fieldset::_getHeaderCommentHtml()
 * @used-by \Df\Payment\Block\Info::checkoutSuccess()
 * @used-by \Frugue\Shipping\Header::_toHtml()
 * @used-by \Dfe\Klarna\Button::_toHtml()
 * @used-by \Dfe\PostFinance\Block\Info::prepare()
 * @used-by \Dfe\Stripe\Block\Form::_toHtml()
 * @param string $tag
 * @param string|array(string => string|string[]|int|null) $attrs [optional]
 * @param string $content [optional]
 * @param bool $multiline [optional]
 * @return string
 */
function df_tag($tag, $attrs = [], $content = null, $multiline = null) {return Html\Tag::render(
	$tag, is_array($attrs) ? $attrs : ['class' => $attrs], $content, $multiline
);}

/**
 * 2016-11-17
 * @param string $text
 * @param string[] $url
 * @return string
 */
function df_tag_a($text, ...$url) {return df_tag('a', ['href' => implode($url)], $text);}

/**
 * 2016-11-17
 * @used-by \Df\Config\Fieldset::_getHeaderCommentHtml()
 * @used-by \Dfe\Moip\Block\Info\Boleto::prepare()
 * @param string $text
 * @param string[] $url
 * @return string
 */
function df_tag_ab($text, ...$url) {return df_tag(
	'a', ['href' => implode($url), 'target' => '_blank'], $text
);}

/**
 * 2016-10-24          
 * @used-by \Df\Payment\Comment\Description::a()
 * @param string $content
 * @param bool $condition
 * @param string $tag
 * @param string|array(string => string|string[]|int|null) $attributes [optional]
 * @param bool $multiline [optional]
 * @return string
 */
function df_tag_if($content, $condition, $tag, $attributes = [], $multiline = null) {return
	!$condition ? $content : df_tag($tag, $attributes, $content, $multiline)
;}

/**
 * @param string[] $items
 * @param bool $isOrdered [optional]
 * @param string|null $cssList [optional]
 * @param string|null $cssItem [optional]
 * @return string
 */
function df_tag_list(array $items, $isOrdered = false, $cssList = null, $cssItem = null) {return
	Html\ListT::render($items, $isOrdered, $cssList, $cssItem
);}