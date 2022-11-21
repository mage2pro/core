<?php
use Df\Core\Format\Html\Tag;
/**
 * 2016-11-13
 * @used-by df_quote_russian()
 * @param string|string[] ...$args
 * @return string|string[]
 */
function df_html_b(...$args) {return df_call_a(function($s) {return df_tag('b', [], $s);}, $args);}

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
 * @used-by \SayItWithAGift\Options\Frontend::_toHtml()
 * @param string|string[] ...$args
 */
function df_link_inline(...$args):string {return df_call_a(function($res) {return df_resource_inline(
	$res, function($url) {return df_tag('link', ['href' => $url, 'rel' => 'stylesheet', 'type' => 'text/css'], null, false);}
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
 * @used-by df_js_inline_url()
 * @used-by df_link_inline()
 */
function df_resource_inline(string $r, Closure $f):string {
	static $c; /** @var array(string => bool) $c */
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
 */
function df_style_inline($css):string {return !$css ? '' : df_tag('style', ['type' => 'text/css'], $css);}

/**
 * 2016-12-04
 * @used-by \Df\Sso\Css::_toHtml()
 * @used-by \Df\Sso\Css::_toHtml()
 * @used-by \Frugue\Shipping\Header::_toHtml()
 * @param string ...$s
 */
function df_style_inline_hide(...$s):string {return !$s ? '' : df_style_inline(
	df_csv_pretty($s) . ' {display: none !important;}'
);}

/**
 * 2015-04-16
 * From now on you can pass an array as an attribute's value: @see \Df\Core\Format\Html\Tag::getAttributeAsText()
 * It can be useful for attrivutes like `class`.
 * 2016-05-30 From now on $attrs could be a string. It is the same as ['class' => $attrs].
 * @used-by cs_quote_description() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/107)
 * @used-by df_js_data()
 * @used-by df_js_x()
 * @used-by df_kv_table()
 * @used-by df_tag_list()
 * @used-by \AlbumEnvy\Popup\Content::_toHtml()
 * @used-by \Df\Config\Fieldset::_getHeaderCommentHtml()
 * @used-by \Df\Framework\Console\Command::execute()
 * @used-by \Df\Payment\Block\Info::checkoutSuccess()
 * @used-by \Dfe\Klarna\Button::_toHtml()
 * @used-by \Dfe\PostFinance\Block\Info::prepare()
 * @used-by \Dfe\Stripe\Block\Form::_toHtml()
 * @used-by \Frugue\Shipping\Header::_toHtml()
 * @used-by \Inkifi\Map\HTML::tiles()
 * @used-by \KingPalm\B2B\Block\Registration::_toHtml()
 * @used-by \KingPalm\B2B\Block\Registration::region()
 * @used-by \TFC\Core\B\Home\Slider::i() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/core/issues/43)
 * @used-by \TFC\Core\B\Home\Slider::p() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/core/issues/43)
 * @used-by \Verdepieno\Core\CustomerAddressForm::f()
 * @used-by \Wolf\Filter\Block\Navigation::hDropdowns()
 * @used-by vendor/mage2pro/color/view/frontend/templates/index.phtml
 * @used-by vendor/wolfautoparts.com/filter/view/frontend/templates/selected_car_onsearchresultpage.phtml
 * @param string|array(string => string|string[]|int|null) $attrs [optional]
 * @param string|null|string[] $content [optional]
 * @param bool|null $multiline [optional]
 */
function df_tag(string $tag, $attrs = [], $content = null, $multiline = null):string {
	$t = new Tag($tag, is_array($attrs) ? $attrs : ['class' => $attrs], $content, $multiline); /** @vat Tag $t */
	return $t->render();
}

/**
 * 2016-11-17
 * @used-by \Df\Config\Fieldset::_getHeaderCommentHtml()
 * @used-by \Dfe\Moip\Block\Info\Boleto::prepare()
 * @param string $text
 * @param string ...$url
 */
function df_tag_ab($text, ...$url):string {return df_tag('a', ['href' => implode($url), 'target' => '_blank'], $text);}

/**
 * 2016-10-24          
 * @used-by \Df\Payment\Comment\Description::a()
 * @used-by \Df\Payment\Method::tidFormat()
 * @used-by \Df\Payment\PlaceOrderInternal::message()
 * @used-by \Df\Sso\Button::_toHtml()
 * @param string $content
 * @param bool $condition
 * @param string $tag
 * @param string|array(string => string|string[]|int|null) $attributes [optional]
 * @param bool $multiline [optional]
 */
function df_tag_if($content, $condition, $tag, $attributes = [], $multiline = null):string {return
	!$condition ? $content : df_tag($tag, $attributes, $content, $multiline)
;}

/**
 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterGetComment()
 * @param string[] $items
 * @param bool $isOrdered [optional]
 * @param string|null $cssList [optional]
 * @param string|null $cssItem [optional]
 */
function df_tag_list(array $items, $isOrdered = false, $cssList = null, $cssItem = null):string {return df_tag(
	$isOrdered ? 'ol' : 'ul'
	,array_filter(['class' => $cssList])
	,df_cc_n(array_map(function($i) use($cssItem) {return df_tag('li', array_filter(['class' => $cssItem]), $i);}, $items))
);}