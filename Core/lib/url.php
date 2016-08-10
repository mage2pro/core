<?php
use Exception as E;
use Magento\Framework\Exception\LocalizedException as LE;

/**
 * 2016-07-12
 * @param string $url
 * @param string|E $message [optional]
 * @return bool
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
 * @param array(string => mixed)|null $routeParams [optional]
 * @return array(string => mixed)
 */
function df_adjust_route_params($routeParams = null) {
	if (!$routeParams) {
		$routeParams = [];
	}
	return ['_nosid' => true] + $routeParams;
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
 * http://mage2.ru/t/topic/37
 * @return string
 */
function df_current_url() {return df_url_o()->getCurrentUrl();}

/**
 * 2015-11-28
 * @param string|null $routePath [optional]
 * @param string|null $routeParams [optional]
 * @return string
 */
function df_url($routePath = null, $routeParams = null) {
	return df_url_o()->getUrl($routePath, df_adjust_route_params($routeParams));
}

/**
 * 2015-11-28
 * @param string|null $routePath [optional]
 * @param string|null $routeParams [optional]
 * @return string
 */
function df_url_backend($routePath = null, $routeParams = null) {
	return df_url_backend_o()->getUrl($routePath, df_adjust_route_params($routeParams));
}

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
 * @param string|null $routePath [optional]
 * @param string|null $routeParams [optional]
 * @return string
 */
function df_url_frontend($routePath = null, $routeParams = null) {
	return df_url_frontend_o()->getUrl($routePath, df_adjust_route_params($routeParams));
}

/** @return \Magento\Backend\Model\Url */
function df_url_backend_o() {return df_o(\Magento\Backend\Model\Url::class);}

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