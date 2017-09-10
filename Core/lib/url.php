<?php
use Df\Backend\Model\Url as UrlBackend;
use Df\Core\Exception as DFE;
use Exception as E;
use Magento\Backend\Model\UrlInterface as IUrlBackend;
use Magento\Framework\App\Route\Config as RouteConfig;
use Magento\Framework\App\Route\ConfigInterface as IRouteConfig;
use Magento\Framework\Exception\LocalizedException as LE;
use Magento\Framework\Url;
use Magento\Framework\UrlInterface as IUrl;
use Magento\Store\Model\Store;

/**
 * @param array(string => mixed) $params [optional]
 * @return array(string => mixed)
 */
function df_adjust_route_params(array $params = []) {return ['_nosid' => true] + $params;}

/**
 * 2016-07-12
 * @used-by df_webhook()
 * @param string $url
 * @param string|E $msg [optional]
 * @return string
 * @throws E|LE
 */
function df_assert_https($url, $msg = null) {return df_check_https_strict($url) ? $url : df_error(
	$msg ?: "The URL «{$url}» is invalid, because the system expects an URL which starts with «https://»."
);}

/**
 * 2016-07-16
 * @param string $url
 * @return bool
 */
function df_check_https($url) {return df_starts_with(strtolower($url), 'https');}

/**
 * 2016-05-30
 * http://framework.zend.com/manual/1.12/en/zend.uri.chapter.html#zend.uri.instance-methods.getscheme
 * @uses \Zend_Uri::getScheme() always returns a lowercased value:
 * @see \Zend_Uri::factory()
 * https://github.com/zendframework/zf1/blob/release-1.12.16/library/Zend/Uri.php#L100
 * $scheme = strtolower($uri[0]);
 * @param string $url
 * @return bool
 */
function df_check_https_strict($url) {return 'https' === df_zuri($url)->getScheme();}

/**
 * http://stackoverflow.com/a/15011528
 * http://www.php.net/manual/en/function.filter-var.php
 * Обратите внимание, что
 * filter_var('/C/A/CA559AWLE574_1.jpg', FILTER_VALIDATE_URL) вернёт false
 * @param $s $string
 * @return bool
 */
function df_check_url($s) {return false !== filter_var($s, FILTER_VALIDATE_URL);}

/**
 * http://mage2.ru/t/37
 * @return string
 */
function df_current_url() {return df_url_o()->getCurrentUrl();}

/**
 * 2017-05-12
 * @used-by df_domain_current()
 * @used-by Dfe_PortalStripe::view/frontend/templates/page/customers.phtml
 * @param string $uri
 * @param bool $www [optional]
 * @param bool $throw [optional]
 * @return string|null
 * @throws \Zend_Uri_Exception
 */
function df_domain($uri, $www = false, $throw = true) {return
	!($r = df_zuri($uri, $throw)->getHost()) ? null : ($www ? $r : df_trim_text_left($r, 'www.'))
;}

/**
 * 2016-08-27
 * @used-by df_webhook()
 * @used-by dfp_url_customer_return()
 * @used-by \Df\Framework\Form\Element\Url::routePath()
 * @used-by \Df\Sso\Button\Js::attributes()
 * @used-by \Df\Sso\FE\CustomerReturn::url()
 * @used-by \Dfe\BlackbaudNetCommunity\Url::get()
 * @param string|object $m
 * $m could be:
 * 1) A module name: «A_B».
 * 2) A class name: «A\B\C».
 * 3) An object. It is reduced to case 2 via @see get_class()
 * @param string|null $path [optional]
 * @param bool $backend [optional]
 * @return string
 * @throws DFE
 */
function df_route($m, $path = null, $backend = false) {
	/** @var string $route */
	$route = df_route_config()->getRouteFrontName($m = df_module_name($m), $backend ? 'adminhtml' : 'frontend');
	if ($m === $route) {
		df_error("df_route(): please define the route for the «{$m}» module in the module's «etc/frontend/routes.xml» file.");
	}
	return df_cc_path($route, $path);
}

/**
 * 2016-08-27
 * @return IRouteConfig|RouteConfig
 */
function df_route_config() {return df_o(IRouteConfig::class);}

/**
 * 2015-11-28
 * @param string|null $path [optional]
 * @param array(string => mixed) $params [optional]
 * @return string
 */
function df_url($path = null, array $params = []) {return df_url_o()->getUrl(
	$path, df_adjust_route_params($params)
);}

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
 * @param array(string => mixed) $params [optional]
 * @return string
 */
function df_url_backend($path = null, array $params = []) {return df_url_trim_index(df_url_backend_new()->getUrl(
	$path, df_adjust_route_params($params)
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
 * @param array(string => mixed) $params [optional]
 * @return string
 */
function df_url_backend_ns($path = null, array $params = []) {return df_url_backend(
	$path, ['_nosecret' => true] + $params
);}

/** @return UrlBackend */
function df_url_backend_o() {return df_o(UrlBackend::class);}

/**
 * 2017-06-28
 * @return UrlBackend
 */
function df_url_backend_new() {return df_new_om(UrlBackend::class);}

/**
 * Пребразует строку вида «превед [[медвед]]» в «превед <a href="http://yandex.ru">медвед</a>».
 * @used-by Df_Admin_Model_Notifier::getMessage()
 * @used-by Df_Admin_Model_Notifier_Settings::getMessage()
 * @param string $text
 * @param string $url
 * @param string $quote [optional]
 * @return string
 */
function df_url_bake($text, $url, $quote = '"') {return
	!df_contains($text, '[[') ? $text : preg_replace("#\[\[([^\]]+)\]\]#u", df_tag_a('$1', $url), $text)
;}

/**
 * 2016-05-31
 * @param string $url
 * @return string
 */
function df_url_base($url) {return df_first(df_url_bp($url));}

/**
 * 2017-02-13
 * «https://mage2.pro/sandbox/dfe-paymill» => [«https://mage2.pro»,  «sandbox/dfe-paymill»]
 * @used-by df_url_base()
 * @used-by df_url_trim_index()
 * @param string $url
 * @return string[]
 */
function df_url_bp($url) {
	/** @var string $base */
	/** @var string $path */
	if (!df_check_url($url)) {
		list($base, $path) = ['', $url];
	}
	else {
		/** @var \Zend_Uri_Http $z */
		$z = df_zuri($url);
		$base = df_ccc(':', "{$z->getScheme()}://{$z->getHost()}", dftr($z->getPort(), ['80' => '']));
		$path = df_trim_left($z->getPath());
	}
	return [$base, $path];
}

/**
 * 2015-11-28
 * 2016-12-01 If $path is null, '', or '/', then the function will return the frontend root URL.
 * 2016-12-01 On the frontend side, the @see df_url() behaves identical to df_url_frontend()
 * @used-by df_webhook()
 * @used-by \Df\Sso\FE\CustomerReturn::url()
 * @param string|null $path [optional]
 * @param array(string => mixed) $params [optional]
 * @param Store|int|string|null $store [optional]
 * @return string
 */
function df_url_frontend($path = null, array $params = [], $store = null) {return df_url_trim_index(
	df_url_frontend_o()->getUrl($path,
		df_adjust_route_params($params) + (is_null($store) ? [] : ['_store' => df_store($store)])
	)
);}

/** @return Url */
function df_url_frontend_o() {return df_o(Url::class);}

/** @return IUrl|Url|IUrlBackend|UrlBackend */
function df_url_o() {return df_o(IUrl::class);}

/**
 * 2017-01-22
 * @used-by dfp_url_api()
 * @param bool $test
 * @param string $tmpl
 * @param string[] $names
 * @param mixed[] ...$args [optional]
 * @return string
 */
function df_url_staged($test, $tmpl, array $names, ...$args) {
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
 * 2017-02-13 Убираем окончания «/», «index/» и «index/index/».
 * @used-by df_url_frontend()
 * @param string $url
 * @return string
 */
function df_url_trim_index($url) {
	/** @var string $base */
	/** @var string $path */
	list($base, $path) = df_url_bp($url);
	/** @var string[] $a */
	$a = df_explode_path($path);
	/** @var int $i */
	$i = count($a) - 1;
	while ($a && in_array($a[$i--], ['', 'index'], true)) {array_pop($a);}
	return df_cc_path($base, df_cc_path($a));
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
 * @param string|object $m
 * $m could be:
 * 1) A module name: «A_B».
 * 2) A class name: «A\B\C».
 * 3) An object. It is reduced to case 2 via @see get_class()
 * @param string $suffix [optional]
 * @param bool $requireHTTPS [optional]
 * @param Store|int|string|null $s [optional]
 * @param array(string => string) $p [optional]
 * @return string
 */
function df_webhook($m, $suffix = '', $requireHTTPS = false, $s = null, $p = []) {
	$path = df_route($m, $suffix); /** @var string $path */
	/** @var string $r */
	$r = df_my_local()
		? "https://mage2.pro/sandbox/$path" . (!$p ? '' : '?' . http_build_query($p))
		: df_url_frontend($path, $p + ['_secure' => $requireHTTPS ? true : null], $s)
	;
	return !$requireHTTPS || df_my_local() ? $r : df_assert_https($r);
}

/**
 * 2016-05-30
 * @used-by df_domain()
 * @param string $uri
 * @param bool $throw [optional]
 * @return \Zend_Uri|\Zend_Uri_Http
 * @throws \Zend_Uri_Exception
 */
function df_zuri($uri, $throw = true) {
	try {
		/** @var \Zend_Uri_Http $result */
		$result = \Zend_Uri::factory($uri);
	}
	catch (\Zend_Uri_Exception $e) {
		if ($throw) {
			throw $e;
		}
		$result = null;
	}
	return $result;
}