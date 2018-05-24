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
 * @param string[] ...$args
 * @return string
 */
function df_cc_br(...$args) {return df_ccc("<br>", dfa_flatten($args));}

/**
 * 2017-07-09
 * @used-by df_api_rr_failed()
 * @used-by \Df\API\Client::p()
 * @used-by \Df\Qa\Context::render()
 * @param array(string => string) $a
 * @param int|null $pad [optional]
 * @return string
 */
function df_cc_kv(array $a, $pad = null) {return df_cc_n(df_map_k(df_clean($a),
	function($k, $v) use($pad) {return
		(!$pad ? "$k: " : df_pad("$k:", $pad))
		.(is_array($v) || (is_object($v) && !method_exists($v, '__toString')) ? "\n" . df_json_encode($v) : $v)
	;}
));}

/**
 * @used-by df_cc_kv()
 * @used-by \Dfe\Stripe\Block\Multishipping::_toHtml()
 * @param string[] ...$args
 * @return string
 */
function df_cc_n(...$args) {return df_ccc("\n", dfa_flatten($args));}

/**
 * 2015-12-01 Отныне всегда используем / вместо DIRECTORY_SEPARATOR.
 * @used-by df_js()
 * @used-by \Df\API\Facade::path()
 * @used-by \Df\Config\Comment::groupPath()
 * @used-by \Df\Config\Source::sibling()
 * @used-by \Df\Intl\Js::_toHtml()
 * @used-by \Doormall\Shipping\Partner\Entity::locations()
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
 * @used-by \Frugue\Shipping\Header::_toHtml()
 * @used-by \Dfe\Square\Block\Info::prepare()
 * @used-by \Dfe\Stripe\Block\Multishipping::cardholder()
 * @used-by dfe_modules_info()
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