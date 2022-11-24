<?php
use Df\Core\Exception as DFE;
use Magento\Store\Model\Store;

/**
 * 2016-08-27
 * @used-by df_webhook()
 * @used-by dfp_url_customer_return()
 * @used-by \Df\Framework\Form\Element\Url::routePath()
 * @used-by \Df\Sso\Button\Js::attributes()
 * @used-by \Df\Sso\FE\CustomerReturn::url()
 * @used-by \Dfe\BlackbaudNetCommunity\Url::get()
 * @param string|object|null $m
 * $m could be:
 * 1) A module name: «A_B»
 * 2) A class name: «A\B\C».
 * 3) An object: it comes down to the case 2 via @see get_class()
 * 4) `null`: it comes down to the case 1 with the «Df_Core» module name.
 * @throws DFE
 */
function df_route($m, string $path = '', bool $backend = false):string {
	/** @var string $route */
	$route = df_route_config()->getRouteFrontName($m = df_module_name($m), $backend ? 'adminhtml' : 'frontend');
	if ($m === $route) {
		df_error("df_route(): please define the route for the «{$m}» module in the module's «etc/frontend/routes.xml» file.");
	}
	return df_cc_path($route, $path);
}

/**
 * 2015-11-28
 * 2019-08-25
 * You can pass query parameters as `df_url($path, ['_query' => [...]])`
 * https://magento.stackexchange.com/a/201787
 * https://github.com/inkifi/map/blob/0.0.4/view/frontend/templates/index/section/2/cities.phtml#L4
 * @used-by df_url_checkout_success()
 * @used-by \Df\OAuth\ReturnT::redirectUrl()
 * @used-by \Frugue\Shipping\Header::_toHtml()
 * @used-by \Mangoit\MediaclipHub\Controller\Index\AddToCart::execute()
 * @used-by vendor/wolfautoparts.com/filter/view/frontend/templates/sidebar.phtml
 * @param string|null $path [optional]
 * @param array(string => mixed) $p [optional]
 */
function df_url($path = null, array $p = []):string {return df_url_o()->getUrl($path, df_nosid() + $p);}

/**
 * 2015-11-28
 * 2017-06-28
 * The latest M2 versions incorrectly caches the backend URLs, ignoring the _nosecret parameters,
 * so I do not use @see \Magento\Backend\Model\Url as a singleton anymore,
 * and use @uses df_url_backend_new() instead.
 * Свежие верии Magento 2 из-за своего некорректного кэширования игнорируют _nosecret,
 * поэтому используем @uses df_url_backend_new()       
 * @used-by df_url_backend_ns()
 * @used-by \Df\Framework\Validator\Currency::message()
 * @param string|null $path [optional]
 * @param array(string => mixed) $p [optional]
 */
function df_url_backend($path = null, array $p = []):string {return df_url_trim_index(df_url_backend_new()->getUrl(
	$path, df_nosid() + $p
));}

/**
 * 2016-08-24
 * @used-by df_customer_backend_url()
 * @used-by df_order_backend_url()
 * @used-by dfe_modules_log() 
 * @used-by \Df\OAuth\App::pCommon()
 * @used-by \Df\Sso\FE\CustomerReturn::url()
 * @used-by df_cm_backend_url()
 * @param string|null $path [optional]
 * @param array(string => mixed) $p [optional]
 */
function df_url_backend_ns($path = null, array $p = []):string {return df_url_backend($path, ['_nosecret' => true] + $p);}

/**
 * 2015-11-28
 * 2016-12-01 If $path is null, '', or '/', then the function will return the frontend root URL.
 * 2016-12-01 On the frontend side, the @see df_url() behaves identical to df_url_frontend()
 * @used-by df_router_redirect()
 * @used-by df_webhook()
 * @used-by \Df\Sso\FE\CustomerReturn::url()
 * @used-by \Frugue\Store\Plugin\Framework\App\FrontControllerInterface::aroundDispatch()
 * @param string|null $path [optional]
 * @param array(string => mixed) $p [optional]
 * @param Store|int|string|null $store [optional]
 */
function df_url_frontend($path = null, array $p = [], $store = null):string {return df_url_trim_index(
	df_url_frontend_o()->getUrl($path, df_nosid() + $p + (is_null($store) ? [] : ['_store' => df_store($store)]))
);}

/**
 * 2017-01-22
 * @used-by dfp_url_api()
 * @used-by \Inkifi\Pwinty\API\Client::urlBase()
 * @param string[] $names
 * @param mixed ...$args [optional]
 */
function df_url_staged(bool $test, string $tmpl, array $names, ...$args):string {
	$r = str_replace('{stage}', $test ? df_first($names) : df_last($names), $tmpl); /** @var string $r */
	/**
	 * 2017-09-10
	 * I have added $args condition here, because the «QIWI Wallet» module does not have args here,
	 * and it has $tmpl like:
	 * https://bill.qiwi.com/order/external/main.action?failUrl=https%3A%2F%2Fmage2.pro%2Fsandbox%2Fdfe-qiwi%2FcustomerReturn%3Ffailure%3D1&iframe=0&pay_source=&shop=488380&successUrl=https%3A%2F%2Fmage2.pro%2Fsandbox%2Fdfe-qiwi%2FcustomerReturn&target=&transaction=ORD-2017%2F09-01090
	 * Such $tmpl will lead @see sprintf() to fail.
	 */
	return !$args ? $r : sprintf($r, ...$args);
}

/**
 * 2016-07-12
 * 2017-04-12
 * Раньше я в локальном сценарии добавлял концевой слеш функцией @see df_cc_path_t().
 * Не пойму, зачем. В нелокальном сценарии слеш не добавляется.
 * @used-by dfp_url_customer_return_remote()
 * @used-by dfp_url_customer_return_remote_f()
 * @used-by \Df\Framework\Form\Element\Url::url()
 * @used-by \Df\Payment\Charge::callback()
 * @used-by \Dfe\Moip\Backend\Enable::dfSaveAfter()
 * @param string|object|null $m
 * $m could be:
 * 1) A module name: «A_B»
 * 2) A class name: «A\B\C».
 * 3) An object: it comes down to the case 2 via @see get_class()
 * 4) `null`: it comes down to the case 1 with the «Df_Core» module name.
 * @param string $suffix [optional]
 * @param bool $requireHTTPS [optional]
 * @param Store|int|string|null $s [optional]
 * @param array(string => string) $p [optional]
 */
function df_webhook($m, $suffix = '', $requireHTTPS = false, $s = null, $p = []):string {
	$path = df_route($m, $suffix); /** @var string $path */
	$r = df_my_local()
		? "https://mage2.pro/sandbox/$path" . (!$p ? '' : '?' . http_build_query($p))
		: df_url_frontend($path, $p + ['_secure' => $requireHTTPS ? true : null], $s)
	; /** @var string $r */
	return !$requireHTTPS || df_my_local() ? $r : df_assert_https($r);
}