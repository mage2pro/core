<?php
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
 * http://mage2.ru/t/topic/37
 * @return string
 */
function df_current_url() {return df_url_o()->getCurrentUrl();}

/**
 * 2016-05-30
 * http://framework.zend.com/manual/1.12/en/zend.uri.chapter.html#zend.uri.instance-methods.getscheme
 * @uses \Zend_Uri::getScheme() always returns a lowercased value:
 * @see \Zend_Uri::factory()
 * https://github.com/zendframework/zf1/blob/release-1.12.16/library/Zend/Uri.php#L100
 * $scheme = strtolower($uri[0]);
 * @param string $uri
 * @return bool
 */
function df_uri_check_https($uri) {return 'https' === df_zuri($uri)->getScheme();}

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