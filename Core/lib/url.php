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