<?php
/**
 * «Dfe\AllPay\W\Handler» => «Dfe_AllPay»
 * 
 * 2016-10-26
 * Функция успешно работает с короткими именами классов:
 * «A\B\C» => «A_B»
 * «A_B» => «A_B»
 * «A» => A»
 * https://3v4l.org/Jstvc
 *
 * 2017-01-27
 * Так как «A_B» => «A_B», то функция успешно работает с именем модуля: она просто возвращает его без изменений.
 * $c could be:
 * 1) A module name: «A_B»
 * 2) A class name: «A\B\C».
 * 3) An object: it comes down to the case 2 via @see get_class()
 * 4) `null`: it comes down to the case 1 with the «Df_Core» module name.
 *
 * @used-by df_asset_name()
 * @used-by df_block_output()
 * @used-by df_con()
 * @used-by df_fe_init()
 * @used-by df_js()
 * @used-by df_js_x()
 * @used-by df_module_dir()
 * @used-by df_module_name_c()
 * @used-by df_package()
 * @used-by df_route()
 * @used-by df_sentry_module()
 * @used-by df_widget() 
 * @used-by \Df\Framework\Plugin\View\Element\AbstractBlock::afterGetModuleName()
 * @used-by \Df\Payment\Method::s()
 * @used-by \Df\Shipping\Method::s()
 * @used-by \Df\Sso\CustomerReturn::execute()
 * @used-by \Justuno\M2\Plugin\App\Router\ActionList::aroundGet()
 * @used-by \TFC\Blog\Plugin\Block\Post\ListPost::afterGetCustomBlogThemeVendor()
 * @param string|object|null $c [optional]
 * @param string $del [optional]
 * @return string
 */
function df_module_name($c = null, $del = '_') {return dfcf(function($c, $del) {return
	implode($del, array_slice(df_explode_class($c), 0, 2))
;}, [$c ? df_cts($c) : 'Df\Core', $del]);}

/**
 * 2017-01-04
 * $c could be:
 * 1) A module name. E.g.: «A_B».
 * 2) A class name. E.g.: «A\B\C».
 * 3) An object. It will be treated as case 2 after @see get_class()
 * @used-by dfs()
 * @used-by dfs_con()
 * @used-by \Df\Framework\Action::module()
 * @used-by \Df\Payment\Block\Info::checkoutSuccess()
 * @param string|object|null $c [optional]
 * @return string
 */
function df_module_name_c($c = null) {return df_module_name($c, '\\');}

/**
 * 2016-08-28 «Dfe\AllPay\W\Handler» => «AllPay»
 * 2016-10-20
 * Нельзя делать параметр $c опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 * @param string|object $c
 * @return string
 */
function df_module_name_short($c) {return dfcf(function($c) {return df_explode_class($c)[1];}, [df_cts($c)]);}

/**
 * 2016-02-16
 * «Dfe\CheckoutCom\Method» => «dfe_checkout_com»
 * 2016-10-20
 * Нельзя делать параметр $c опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 * 2017-10-03
 * $c could be:
 * 1) A module name. E.g.: «A_B».
 * 2) A class name. E.g.: «A\B\C».
 * 3) An object. It will be treated as case 2 after @see get_class()  
 * @used-by df_report_prefix()
 * @used-by \Df\Core\Exception::reportNamePrefix()
 * @used-by \Df\Payment\Method::codeS()
 * @used-by \Df\Shipping\Method::getCarrierCode()
 * @used-by \Dfe\Stripe\Block\Multishipping::_toHtml()
 * @param string|object $c
 * @param string $del [optional]
 * @return string
 */
function df_module_name_lc($c, $del = '_') {return implode($del, df_explode_class_lc_camel(df_module_name_c($c)));}