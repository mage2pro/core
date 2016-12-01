<?php
use Exception as E;
use Magento\Store\Model\Store;
use Magento\Framework\App\Route\Config as RouteConfig;
use Magento\Framework\App\Route\ConfigInterface as IRouteConfig;
use Magento\Framework\Exception\LocalizedException as LE;

/**
 * @param array(string => mixed) $params [optional]
 * @return array(string => mixed)
 */
function df_adjust_route_params(array $params = []) {return ['_nosid' => true] + $params;}

/**
 * 2016-07-12
 * @param string $url
 * @param string|E $message [optional]
 * @return void
 * @throws E|LE
 */
function df_assert_https($url, $message = null) {
	if (df_enable_assertions() && !df_check_https($url)) {
		df_error($message ? $message : df_sprintf(
			'The URL «%s» is invalid, because the system expects an URL which starts with «https://».'
			, $url
		));
	}
}

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
 * @param string|object $module
 * @param string|null $scope [optional]
 * @return string
 */
function df_route($module, $scope = 'frontend') {return dfcf(function($m, $s) {return
	df_route_config()->getRouteFrontName($m, $s)
;}, [df_module_name($module), $scope]);}

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

/** @return \Magento\Backend\Model\Url */
function df_url_backend_o() {return df_o(\Magento\Backend\Model\Url::class);}

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
 * 2016-07-12
 * @param string $routePath
 * @param bool $requireHTTPS [optional]
 * @return string
 */
function df_url_callback($routePath, $requireHTTPS = false) {
	/** @var string $result */
	$result =
		df_my_local()
		? df_cc_path_t('https://mage2.pro/sandbox', $routePath)
		: df_url_frontend($routePath, ['_secure' => $requireHTTPS ? true : null])
	;
	if ($requireHTTPS && !df_my_local()) {
		df_assert_https($result);
	}
	return $result;
}

/**
 * 2015-11-28
 * @param string|null $path [optional]
 * 2016-12-01
 * If $path is null, '', or '/', then the function will return the frontend root URL.
 * @param array(string => mixed) $params [optional]
 * @param Store|int|string|null $store [optional]
 * @return string
 * 2016-12-01
 * On the frontend side, the @see df_url() behaves identical to df_url_frontend()
 */
function df_url_frontend($path = null, array $params = [], $store = null) {return
	df_url_frontend_o()->getUrl($path,
		df_adjust_route_params($params) + (is_null($store) ? [] : ['_store' => df_store($store)])
	)
;}

/** @return \Magento\Framework\Url */
function df_url_frontend_o() {return df_o(\Magento\Framework\Url::class);}

/** @return \Magento\Framework\UrlInterface|\Magento\Framework\Url|\Magento\Backend\Model\UrlInterface|\Magento\Backend\Model\Url */
function df_url_o() {return df_o(\Magento\Framework\UrlInterface::class);}

/**
 * 2016-05-31
 * @param string $url
 * @return string
 */
function df_url_strip_path($url) {
	/** @var \Zend_Uri_Http $z */
	$z = df_zuri($url);
	/** @var string $port */
	$port = $z->getPort();
	if ('80' === $port) {
		$port = '';
	}
	if ($port) {
		$port = ':' . $port;
	}
	return $z->getScheme() . '://' . $z->getHost() . $port;
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