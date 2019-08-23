<?php
/**
 * 2016-10-17
 * @param string|string[] ...$elements
 * @return string
 */
function df_c(...$elements) {return implode(dfa_flatten($elements));}

/**
 * @see df_ccc()
 * @used-by df_js_data()
 * @param string $glue
 * @param string|string[] ...$elements
 * @return string
 */
function df_cc($glue, ...$elements) {return implode($glue, dfa_flatten($elements));}

/**
 * 2016-08-13
 * @used-by \Df\Payment\Settings::messageFailure()
 * @used-by \Dfe\AllPay\Choice::title()
 * @used-by \Dfe\Square\API\Validator::short()
 * @used-by \Stock2Shop\OrderExport\Observer\OrderSaveAfter::execute()
 * @param string[] ...$args
 * @return string
 */
function df_cc_br(...$args) {return df_ccc("<br>", dfa_flatten($args));}

/**
 * @used-by df_format_kv()
 * @used-by \Df\Core\Format\Html\Tag::content()
 * @used-by \Dfe\Stripe\Block\Multishipping::_toHtml()
 * @used-by \Inkifi\Map\HTML::tiles()
 * @used-by \SayItWithAGift\Options\Frontend::_toHtml()
 * @used-by \Verdepieno\Core\CustomerAddressForm::f()
 * @param string[] ...$args
 * @return string
 */
function df_cc_n(...$args) {return df_ccc("\n", dfa_flatten($args));}

/**
 * 2015-12-01 Отныне всегда используем / вместо DIRECTORY_SEPARATOR.
 * @used-by df_db_name()
 * @used-by df_fs_etc()
 * @used-by df_img_resize()
 * @used-by df_js()   
 * @used-by df_js_x()
 * @used-by \Df\API\Facade::path()
 * @used-by \Df\Config\Comment::groupPath()
 * @used-by \Df\Config\Source::sibling()
 * @used-by \Df\Intl\Js::_toHtml()
 * @used-by \Doormall\Shipping\Partner\Entity::locations()
 * @used-by \Inkifi\Mediaclip\API\Client::urlBase()
 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload\Pureprint::writeLocal()
 * @param string[] ...$args
 * @return string
 */
function df_cc_path(...$args) {return df_ccc('/', dfa_flatten($args));}

/**
 * 2016-05-31
 * @param string[] ...$args
 * @return string
 */
function df_cc_path_t(...$args) {return df_append(df_cc_path(dfa_flatten($args)), '/');}

/**
 * 2016-08-10
 * @used-by df_block_output()
 * @used-by dfe_modules_info()
 * @used-by \Dfe\Square\Block\Info::prepare()
 * @used-by \Dfe\Stripe\Block\Multishipping::cardholder()
 * @used-by \Frugue\Shipping\Header::_toHtml()
 * @used-by \Inkifi\Mediaclip\API\Client::headers()
 * @used-by \KingPalm\B2B\Block\Registration::_toHtml()
 * @used-by \KingPalm\B2B\Block\Registration::e()
 * @used-by \KingPalm\B2B\Block\Registration::region()
 * @used-by \KingPalm\B2B\Observer\RegisterSuccess::execute()
 * @param string[] ...$args
 * @return string
 */
function df_cc_s(...$args) {return df_ccc(' ', dfa_flatten($args));}

/**
 * @see df_cc()
 * @param string $glue
 * @param string[] ...$elements
 * @return string
 */
function df_ccc($glue, ...$elements) {return implode($glue, df_clean(dfa_flatten($elements)));}