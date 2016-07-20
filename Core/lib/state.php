<?php
use Magento\Framework\App\ProductMetadata;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\State;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;
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
function df_domain($store = null) {
	/** @var string $result */
	$store = df_store($store);
	/** @var int $cacheId */
	$cacheId = $store->getId();
	/** @var array(int => string) $cache */
	static $cache;
	if (!isset($cache[$cacheId])) {
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
		$cache[$cacheId] = $result;
	}
	return $cache[$cacheId];
}

/**
 * https://mage2.ru/t/94
 * https://mage2.pro/t/59
 * @return bool
 */
function df_is_ajax() {static $r; return !is_null($r) ? $r : $r = df_request_o()->isXmlHttpRequest();}

/**
 * 2015-08-14
 * Мы не вправе кэшировать результат работы функции: ведь текущий магазин может меняться.
 * @return bool
 */
function df_is_backend() {
	/**
	 * 2015-09-20
	 * В отличие от Magento 1.x мы не можем использовать код
	 * Magento\Store\Model\Store::ADMIN_CODE === df_store($store)->getCode();
	 * потому что при нахождении в административной части
	 * он вернёт вовсе не административную витрину, а витрину, указанную в MAGE_RUN_CODE.
	 * Более того, @see df_store() учитывает параметры URL
	 * и даже при нахождении в административном интерфейсе
	 * может вернуть вовсе не административную витрину.
	 * Поэтому определяем нахождение в административном интерфейсе другим способом.
	 */
	return 'adminhtml' === df_app_state()->getAreaCode();
}

/**
 * 2016-06-02
 * Сделал по аналогии с @see df_is_backend()
 * @return bool
 */
function df_is_frontend() {return 'frontend' === df_app_state()->getAreaCode();}

/**
 * 2015-12-09
 * https://mage2.pro/t/299
 * @return bool
 */
function df_is_dev() {return State::MODE_DEVELOPER === df_app_state()->getMode();}

/** @return bool */
function df_my_local() {
	/** @var bool $result  */
	static $result;
	if (is_null($result)) {
		$result = df_bool(dfa($_SERVER, 'RM_DEVELOPER'));
	}
	return $result;
}

/**
 * 2016-05-15
 * http://stackoverflow.com/a/2053295
 * @return bool
 */
function df_is_localhost() {return in_array(dfa($_SERVER, 'REMOTE_ADDR', []), ['127.0.0.1', '::1']);}

/**
 * 2016-06-25
 * https://mage2.pro/t/543
 */
function df_magento_version() {return df_magento_version_m()->getVersion();}

/**
 * 2016-06-25
 * https://mage2.pro/t/543
 */
function df_magento_version_full() {
	return implode(' ', [
		df_magento_version_m()->getName()
		, df_magento_version_m()->getEdition()
		, 'Edition'
		, df_magento_version_m()->getVersion()
	]);
}

/**
 * 2016-06-25
 * @return ProductMetadata|ProductMetadataInterface
 */
function df_magento_version_m() {return df_o(ProductMetadataInterface::class);}

/**
 * @param string $key
 * @param mixed $value
 * @return void
 */
function df_register($key, $value) {df_registry_o()->register($key, $value);}

/**
 * @param string $key
 * @return mixed
 */
function df_registry($key) {return df_registry_o()->registry($key);}

/**
 * 2015-11-02
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
