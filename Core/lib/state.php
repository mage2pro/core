<?php
use Magento\Framework\App\Area;
use Magento\Framework\App\ProductMetadata;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\State;
use Magento\Store\Api\Data\StoreInterface;
/**
 * 2015-12-21
 * @return bool
 */
function df_action_catalog_product_view() {return df_action_is('catalog_product_view');}

/**
 * 2016-01-07
 * @param string|string[] $name
 * @return string|bool
 */
function df_action_is($name) {
	/** @var string $actionName */
	$actionName = df_action_name();
	return 1 === func_num_args()
		? $actionName === $name
		: in_array($name, df_args(func_get_args()))
	;
}

/**
 * 2015-09-02
 * @return string|bool
 */
function df_action_name() {return df_request_o()->getFullActionName();}

/**
 * 2015-09-20
 * @used-by df_is_backend()
 * @return State
 */
function df_app_state() {return df_o(\Magento\Framework\App\State::class);}

/**
 * @return \Magento\Framework\App\Action\Action|null
 */
function df_controller() {return df_state()->controller();}

/**
 * 2016-03-09
 * Портировал из РСМ.
 * @param int|string|null|bool|StoreInterface $store [optional]
 * @return string|null
 */
function df_domain($store = null) {return dfcf(function($store = null) {
	/** @var string $result */
	$store = df_store($store);
	/** @var string|null $baseUrl */
	// Может вернуть null, если в БД отсутствует значение соответствующей опции.
	$baseUrl = $store->getBaseUrl();
	if ($baseUrl) {
		try {
			$result = df_zuri($baseUrl)->getHost();
			df_assert_string_not_empty($result);
		}
		catch (Exception $e) {}
	}
	if (!$result) {
		/** @var \Zend_View_Helper_ServerUrl $helper */
		$helper = new \Zend_View_Helper_ServerUrl();
		/** @var string|null $result */
		// Может вернуть null, если Magento запущена с командной строки (
		// например, планировщиком задач)
		$result = $helper->getHost();
	}
	return $result;
}, func_get_args());}

/**
 * https://mage2.ru/t/94
 * https://mage2.pro/t/59
 * @return bool
 */
function df_is_ajax() {static $r; return !is_null($r) ? $r : $r = df_request_o()->isXmlHttpRequest();}

/**
 * 2016-08-24
 * @return bool
 */
function df_is_checkout() {return df_handle('checkout_index_index');}

/**
 * 2015-12-09
 * https://mage2.pro/t/299
 * @return bool
 */
function df_is_dev() {return State::MODE_DEVELOPER === df_app_state()->getMode();}

/**
 * 2016-05-15
 * http://stackoverflow.com/a/2053295
 * @return bool
 */
function df_is_localhost() {return in_array(dfa($_SERVER, 'REMOTE_ADDR', []), ['127.0.0.1', '::1']);}

/**
 * 2016-12-22
 * @return bool
 */
function df_is_windows() {return dfcf(function() {return 'WIN' === strtoupper(substr(PHP_OS, 0, 3));});}

/**
 * 2016-06-25
 * https://mage2.pro/t/543
 */
function df_magento_version() {return df_magento_version_m()->getVersion();}

/**
 * 2016-08-24
 * @param string $version
 * @return bool
 */
function df_magento_version_ge($version) {
	return version_compare(df_magento_version(), $version, 'ge');
}

/**
 * 2016-06-25
 * https://mage2.pro/t/543
 */
function df_magento_version_full() {
	/** @var ProductMetadata|ProductMetadataInterface $v */
	$v = df_magento_version_m();
	return df_cc_s($v->getName(), $v->getEdition(), 'Edition', $v->getVersion());
}

/**
 * 2016-06-25
 * @return ProductMetadata|ProductMetadataInterface
 */
function df_magento_version_m() {return df_o(ProductMetadataInterface::class);}

/** @return bool */
function df_my() {return dfcf(function() {return df_bool(dfa($_SERVER, 'DF_DEVELOPER'));});}

/** @return bool */
function df_my_local() {return dfcf(function() {return df_my() && df_is_localhost();});}

/**
 * @param string $key
 * @param mixed $value
 * @return void
 */
function df_register($key, $value) {df_registry_o()->register($key, $value);}

/**
 * @param string $key
 * @return mixed|null
 */
function df_registry($key) {return df_registry_o()->registry($key);}

/**
 * 2015-11-02
 * @used-by df_register()
 * @used-by df_registry()
 * https://mage2.pro/t/95
 * @return \Magento\Framework\Registry
 */
function df_registry_o() {return df_o(\Magento\Framework\Registry::class);}

/**
 * @param string|null $key [optional]
 * @param string|null|callable $default [optional]
 * @return string|array(string => string)
 */
function df_request($key = null, $default = null) {
	/** @var string|array(string => string) $result */
	if (is_null($key)) {
		$result = df_request_o()->getParams();
	}
	else {
		$result = df_request_o()->getParam($key);
		$result = df_if1(is_null($result) || '' === $result, $default, $result);
	}
	return $result;
}

/**
 * 2015-08-14
 * https://github.com/magento/magento2/issues/1675
 * @return \Magento\Framework\App\RequestInterface|\Magento\Framework\App\Request\Http
 */
function df_request_o() {return df_o(\Magento\Framework\App\RequestInterface::class);}

/**
 * 2015-08-14
 * @return string
 */
function df_ruri() {static $r; return $r ? $r : $r = df_request_o()->getRequestUri();}

/**
 * 2015-08-14
 * @param string $needle
 * @return bool
 */
function df_ruri_contains($needle) {return df_contains(df_ruri(), $needle);}

/**
 * @return \Df\Core\State
 */
function df_state() {static $r; return $r ? $r : $r = \Df\Core\State::s();}