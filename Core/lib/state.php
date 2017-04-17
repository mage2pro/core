<?php
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
 * 2017-03-16
 * @used-by \Dfe\AllPay\W\Event::needCapture()
 * @param string $s
 * @return bool
 */
function df_action_has($s) {return df_contains(df_action_name(), $s);}

/**
 * 2016-01-07
 * @param string[] ...$names
 * @return bool
 */
function df_action_is(...$names) {return ($a = df_action_name()) && in_array($a, dfa_flatten($names));}

/**
 * 2015-09-02
 * 2017-03-15
 * Случай запуска Magento с командной строки надо обрабатывать отдельно, потому что иначе
 * @uses \Magento\Framework\App\Request\Http::getFullActionName() вернёт строку «__».
 * @used-by df_action_has()
 * @used-by df_action_is()
 * @used-by df_sentry()                          
 * @used-by \Dfe\AllPay\W\Event::needCapture()
 * @used-by \Dfe\Markdown\CatalogAction::entityType()
 * @used-by \Dfe\Markdown\FormElement::config()
 * @return string|null
 */
function df_action_name() {return df_is_cli() ? null : df_request_o()->getFullActionName();}

/**
 * 2015-09-20
 * @used-by df_is_backend()
 * @return State
 */
function df_app_state() {return df_o(State::class);}

/**
 * @return \Magento\Framework\App\Action\Action|null
 */
function df_controller() {return df_state()->controller();}

/**
 * 2016-03-09
 * I have ported it from my «Russian Magento» product for Magento 1.x: http://magento-forum.ru
 * 2017-03-15
 * It returns null only if the both conditions are true:
 * 1) Magento runs from the command line (by Cron or in console).
 * 2) The store's root URL is absent in the Magento database.
 * @used-by df_modules_log()
 * @used-by df_sentry()
 * @used-by \Df\Payment\Metadata::vars()
 * @param int|string|null|bool|StoreInterface $s [optional]
 * @param bool $www [optional]
 * @return string|null
 */
function df_domain($s = null, $www = false) {return dfcf(function($s = null, $www = false) {return
	/**
	 * 2016-03-09
	 * @uses df_store_url_web() returns an empty string
	 * if the store's root URL is absent in the Magento database.
	 * @var string|null $base
	 * @var \Zend_Uri_Http|null $z
	 */
	($r = (($base = df_store_url_web($s)) && ($z = df_zuri($base, false))) ? $z->getHost() :
		/**
		* 2017-03-15
		* @uses \Magento\Framework\HTTP\PhpEnvironment\Request::getHttpHost()
		* returns false, if Magento runs from the command line (by Cron or in console).
		* Previously, I have used another (similar) solution: @see \Zend_View_Helper_ServerUrl::getHost()
		*/
		(df_request_o()->getHttpHost() ?: null)
	) && $www ? $r : df_trim_text_left($r, 'www.')
;}, func_get_args());}

/**
 * https://mage2.ru/t/94
 * https://mage2.pro/t/59
 * @return bool
 */
function df_is_ajax() {static $r; return !is_null($r) ? $r : $r = df_request_o()->isXmlHttpRequest();}



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
 * @used-by df_sentry()
 */
function df_magento_version() {return df_magento_version_m()->getVersion();}

/**
 * 2016-08-24
 * @param string $version
 * @return bool
 */
function df_magento_version_ge($version) {return version_compare(df_magento_version(), $version, 'ge');}

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

/**
 * 2017-04-17
 * Вторая ветка условия —
 * для ситуации работы программы с командной строки на моём локлаьном компьютере.
 * @return bool
 */
function df_my() {return dfcf(function() {return
	df_bool(dfa($_SERVER, 'DF_DEVELOPER')) || ('dfediuk' === dfa($_SERVER, 'USERNAME'))
;});}

/** @return bool */
function df_my_local() {return dfcf(function() {return df_my() && df_is_localhost();});}

/**
 * @see df_registry()
 * @param string $key
 * @param mixed $value
 */
function df_register($key, $value) {df_registry_o()->register($key, $value);}

/**
 * @see df_register()
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
 * @return \Df\Core\State
 */
function df_state() {static $r; return $r ? $r : $r = \Df\Core\State::s();}