<?php
/**
 * «Dfe\AllPay\W\Handler» => «Dfe_AllPay»
 * 2016-10-26
 * The function correctly handles class names without a namespace and with the `_` character:
 * 		«A\B\C» => «A_B»
 * 		«A_B» => «A_B»
 * 		«A» => A»
 * 		https://3v4l.org/Jstvc
 * 2017-01-27
 * $c could be:
 * 		1) a module name: «A_B»
 * 		2) a class name: «A\B\C».
 * 		3) an object: it comes down to the case 2 via @see get_class()
 * 		4) `null`: it comes down to the case 1 with the «Df_Core» module name.
 * @used-by df_asset_name()
 * @used-by df_block_output()
 * @used-by df_con()
 * @used-by df_fe_init()
 * @used-by df_js()
 * @used-by df_js_x()
 * @used-by df_module_dir()
 * @used-by df_module_enabled()
 * @used-by df_module_name_c()
 * @used-by df_package()
 * @used-by df_route()
 * @used-by df_sentry_module()
 * @used-by df_widget()
 * @used-by dfpm_title()
 * @used-by \Df\Core\Session::__construct()
 * @used-by \Df\Framework\Plugin\View\Element\AbstractBlock::afterGetModuleName()
 * @used-by \Df\Payment\Method::s()
 * @used-by \Df\Shipping\Method::s()
 * @used-by \Df\Sso\CustomerReturn::execute()
 * @used-by \TFC\Blog\Plugin\Block\Post\ListPost::afterGetCustomBlogThemeVendor()
 * @param string|object|null $c [optional]
 */
function df_module_name($c = null, string $del = '_'):string {return dfcf(
	function(string $c, string $del):string {return implode($del, array_slice(df_explode_class($c), 0, 2));}
	,[$c ? df_cts($c) : 'Df\Core', $del]
);}

/**
 * 2017-01-04
 * 		$c could be:
 * 		1) a module name. E.g.: «A_B».
 * 		2) a class name. E.g.: «A\B\C».
 * 		3) an object. It will be treated as case 2 after @see get_class()
 * @used-by df_module_name_lc()
 * @used-by dfp_report()
 * @used-by dfs()
 * @used-by dfs_con()
 * @used-by \Df\Framework\Action::module()
 * @used-by \Df\Payment\Block\Info::checkoutSuccess()
 * @param string|object|null $c [optional]
 */
function df_module_name_c($c = null):string {return df_module_name($c, '\\');}

/**
 * 2016-08-28 «Dfe\AllPay\W\Handler» => «AllPay»
 * 2016-10-20
 * Making $c optional leads to the error «get_class() called without object from outside a class»: https://3v4l.org/k6Hd5
 * @param string|object $c
 */
function df_module_name_short($c):string {return dfcf(function($c) {return df_explode_class($c)[1];}, [df_cts($c)]);}

/**
 * 2016-02-16 «Dfe\CheckoutCom\Method» => «dfe_checkout_com»
 * 2016-10-20
 * Making $c optional leads to the error «get_class() called without object from outside a class»: https://3v4l.org/k6Hd5
 * 2017-10-03
 * $c could be:
 * 1) a module name. E.g.: «A_B».
 * 2) a class name. E.g.: «A\B\C».
 * 3) an object. It will be treated as case 2 after @see get_class()
 * @used-by \Df\Payment\Method::codeS()
 * @used-by \Df\Shipping\Method::getCarrierCode()
 * @used-by \Dfe\Stripe\Block\Multishipping::_toHtml()
 * @see df_cts_lc_camel()
 * @param string|object $c
 * @param string $del [optional]
 */
function df_module_name_lc($c, $del = '_'):string {return implode($del, df_explode_class_lc_camel(df_module_name_c($c)));}