<?php
use Exception as E;
use Magento\Backend\Model\Url as UrlBackend;
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
 * @param string $url
 * @param string|E $msg [optional]
 * @return string
 * @throws E|LE
 */
function df_assert_https($url, $msg = null) {return df_check_https($url) ? $url : df_error(
	$msg ?: "The URL «{$url}» is invalid, because the system expects an URL which starts with «https://»."
);}

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
function df_check_https($url) {return 'https' === df_zuri($url)->getScheme();}

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
 * http://mage2.ru/t/topic/37
 * @return string
 */
function df_current_url() {return df_url_o()->getCurrentUrl();}

/**
 * 2016-08-27
 * @param string|object $m
 * Функция допускает в качестве $m:
 * 1) Имя модуля. Например: «A_B».
 * 2) Имя класса. Например: «A\B\C».
 * 3) Объект. Сводится к случаю 2 посредством @see get_class()
 * @used-by dfp_url_callback()
 * @used-by dfp_url_customer_return()
 * @used-by \Df\Framework\Form\Element\Url::routePath()
 * @used-by \Df\Sso\Button\Js::attributes()
 * @used-by \Df\Sso\FE\CustomerReturn::url()
 * @used-by \Dfe\BlackbaudNetCommunity\Url::get()
 * @param string|null $path [optional]
 * @return string
 */
function df_route($m, $path = null) {return df_cc_path(
	df_route_config()->getRouteFrontName(df_module_name($m), 'frontend'), $path
);}

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
function df_url($path = null, array $params = []) {return
	df_url_o()->getUrl($path, df_adjust_route_params($params))
;}

/**
 * 2015-11-28
 * @param string|null $path [optional]
 * @param array(string => mixed) $params [optional]
 * @return string
 */
function df_url_backend($path = null, array $params = []) {return
	df_url_backend_o()->getUrl($path, df_adjust_route_params($params))
;}

/**
 * 2016-08-24
 * @param string|null $path [optional]
 * @param array(string => mixed) $params [optional]
 * @return string
 */
function df_url_backend_ns($path = null, array $params = []) {return
	df_url_backend($path, ['_nosecret' => true] + $params)
;}

/** @return UrlBackend */
function df_url_backend_o() {return df_o(UrlBackend::class);}

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
 * 2016-07-12
 * 2017-04-12
 * Раньше я в локальном сценарии добавлял концевой слеш функцией @see df_cc_path_t().
 * Не пойму, зачем. В нелокальном сценарии слеш не добавляется.
 * @param string $path
 * @param bool $requireHTTPS [optional]
 * @return string
 */
function df_url_callback($path, $requireHTTPS = false) {
	/** @var string $result */
	$result = df_my_local() ? df_cc_path('https://mage2.pro/sandbox', $path) : df_url_frontend($path, [
		'_secure' => $requireHTTPS ? true : null
	]);
	return !$requireHTTPS || df_my_local() ? $result : df_assert_https($result);
}

/**
 * 2015-11-28
 * 2016-12-01 If $path is null, '', or '/', then the function will return the frontend root URL.
 * 2016-12-01 On the frontend side, the @see df_url() behaves identical to df_url_frontend()
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
function df_url_staged($test, $tmpl, array $names, ...$args) {return sprintf(
	str_replace('{stage}', $test ? df_first($names) : df_last($names), $tmpl)
, ...$args);}

/**
 * 2017-02-13
 * Убираем окончания «/», «index/» и «index/index/».
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
 * 2016-05-30
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