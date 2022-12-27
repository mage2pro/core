<?php
use Df\Core\Format\Html\Tag;
/**
 * 2016-11-13
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax will reject arrays: https://3v4l.org/jFdPm
 * @used-by df_quote_russian()
 * @param string|string[] $a
 * @return string|string[]
 */
function df_html_b(...$a) {return df_call_a(function(string $s) {return df_tag('b', [], $s);}, $a);}

/**
 * 2015-10-27
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax will reject arrays: https://3v4l.org/jFdPm
 * @used-by df_fa_link()
 * @used-by df_fe_init()
 * @used-by \Df\Phone\Js::_toHtml()
 * @used-by \Dfe\Customer\Block::_toHtml()
 * @used-by \Dfe\Frontend\Block\ProductView\Css::_toHtml()
 * @used-by \Dfe\Klarna\Button::_toHtml()
 * @used-by \Dfe\Markdown\FormElement::css()
 * @used-by \Dfe\Stripe\Block\Multishipping::_toHtml()
 * @used-by \SayItWithAGift\Options\Frontend::_toHtml()
 * @param string|string[] $a
 * @return string|string[]
 */
function df_link_inline(...$a) {return df_call_a(function(string $res):string {return df_resource_inline(
	$res, function(string $url):string {return df_tag(
		'link', ['href' => $url, 'rel' => 'stylesheet', 'type' => 'text/css'], '', false
	);}
);}, $a);}

/**
 * 2015-12-11
 * Применяем кэширование, чтобы не загружать повторно один и тот же файл CSS.
 * Как оказалось, браузер при наличии на странице нескольких тегов link с одинаковым адресом
 * применяет одни и те же правила несколько раз (хотя, видимо, не делает повторных обращений к серверу
 * при включенном в браузере кэшировании браузерных ресурсов).
 * 2016-03-23
 * Добавил обработку пустой строки $u.
 * Нам это нужно, потому что пустую строку может вернуть @see \Df\Typography\Font::link()
 * https://mage2.pro/t/1010
 * @used-by df_js_inline_url()
 * @used-by df_link_inline()
 */
function df_resource_inline(string $u, Closure $f):string {
	static $c; /** @var array(string => bool) $c */
	if (!$u || isset($c[$u])) {$r = '';}
	else {$c[$u] = true; $r = $f(df_asset_create($u)->getUrl());}
	return $r;
}

/**
 * 2015-12-21
 * 2015-12-25: Пустой тег style приводит к белому экрану в Chrome: <style type='text/css'/>.
 * @used-by df_style_inline_hide()
 * @used-by \Df\Sso\Button::loggedOut()
 * @used-by \Dfe\Frontend\Block\ProductView\Css::_toHtml()
 */
function df_style_inline(string $css):string {return !$css ? '' : df_tag('style', ['type' => 'text/css'], $css);}

/**
 * 2016-12-04
 * @used-by \Df\Sso\Css::_toHtml()
 * @used-by \Df\Sso\Css::_toHtml()
 * @used-by \Frugue\Shipping\Header::_toHtml()
 */
function df_style_inline_hide(string ...$s):string {return !$s ? '' : df_style_inline(
	df_csv_pretty($s) . ' {display: none !important;}'
);}

/**
 * 2015-04-16
 * From now on you can pass an array as an attribute's value: @see \Df\Core\Format\Html\Tag::getAttributeAsText()
 * It can be useful for attrivutes like `class`.
 * 2016-05-30 From now on $attrs could be a string. It is the same as ['class' => $attrs].
 * @used-by cs_quote_description() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/107)
 * @used-by df_caller_mh()
 * @used-by df_html_b()
 * @used-by df_js_data()
 * @used-by df_js_inline_r()
 * @used-by df_js_inline_url()
 * @used-by df_js_x()
 * @used-by df_kv_table()
 * @used-by df_link_inline()
 * @used-by df_style_inline()
 * @used-by df_tag_ab()
 * @used-by df_tag_if()
 * @used-by df_tag_list()
 * @used-by \AlbumEnvy\Popup\Content::_toHtml()
 * @used-by \Df\Backend\Block\Widget\Form\Renderer\Fieldset\Element::_render()
 * @used-by \Df\Backend\Block\Widget\Form\Renderer\Fieldset\Element::inner1()
 * @used-by \Df\Backend\Block\Widget\Form\Renderer\Fieldset\Element::innerRow()
 * @used-by \Df\Backend\Block\Widget\Form\Renderer\Fieldset\Element::note()
 * @used-by \Df\Backend\Block\Widget\Grid\Column\Renderer\Text::render()
 * @used-by \Df\Config\Fieldset::_getHeaderCommentHtml()
 * @used-by \Df\Config\Plugin\Block\System\Config\Form\Fieldset::aroundRender()
 * @used-by \Df\Framework\Console\Command::execute()
 * @used-by \Df\Framework\Form\Element\Checkbox::getElementHtml()
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
 * @used-by https://github.com/tradefurniturecompany/report/blob/1.0.3/view/frontend/templates/index.phtml#L31
 * @param string|array(string => string|string[]|int|null) $attrs [optional]
 * @param string|string[] $content [optional]
 * @param bool|null $multiline [optional]
 */
function df_tag(string $tag, $attrs = [], $content = '', $multiline = null):string {
	$t = new Tag($tag, is_array($attrs) ? $attrs : ['class' => $attrs], $content, $multiline); /** @vat Tag $t */
	return $t->render();
}

/**
 * 2016-11-17
 * @used-by \Df\Config\Fieldset::_getHeaderCommentHtml()
 * @used-by \Dfe\AllPay\Block\Info\BankCard::allpayAuthCode()
 * @used-by \Dfe\Moip\Block\Info\Boleto::prepare()
 * @used-by \Dfe\TwoCheckout\Block\Info::prepare()
 */
function df_tag_ab(string $s, string $u):string {return df_tag('a', ['href' => $u, 'target' => '_blank'], $s);}

/**
 * 2016-10-24          
 * @used-by \Df\Payment\Comment\Description::a()
 * @used-by \Df\Payment\Method::tidFormat()
 * @used-by \Df\Payment\PlaceOrderInternal::message()
 * @used-by \Df\Sso\Button::_toHtml()
 * @param string|array(string => string|string[]|int|null) $attributes [optional]
 * @param bool|string $multiline [optional]
 */
function df_tag_if(string $content, bool $condition, string $tag, $attributes = [], $multiline = null):string {return
	!$condition ? $content : df_tag($tag, $attributes, $content, $multiline)
;}

/**
 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterGetComment()
 * @param string[] $items
 * @param string|null $cssList [optional]
 * @param string|null $cssItem [optional]
 */
function df_tag_list(array $items, bool $isOrdered = false, $cssList = null, $cssItem = null):string {return df_tag(
	$isOrdered ? 'ol' : 'ul'
	,array_filter(['class' => $cssList])
	,df_cc_n(array_map(function($i) use($cssItem) {return df_tag('li', array_filter(['class' => $cssItem]), $i);}, $items))
);}