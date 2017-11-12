<?php
/**
 * 2017-01-25
 * @used-by df_sentry()
 * @used-by df_sentry_m()
 * @used-by \Df\Sentry\Client::__construct()
 * @used-by \Df\Sentry\Client::getUserAgent()
 * @used-by \Dfe\Klarna\Api\Checkout\V3\UserAgent::__construct()
 * @return string
 */
function df_core_version() {return dfcf(function() {return df_package_version('Df_Core');});}

/**
 * 2017-01-10
 * Эта функция считывает информацию из локального файла «composer.json» того модуля,
 * которому принадлежит класс $m.
 * $m could be:
 * 1) A module name: «A_B».
 * 2) A class name: «A\B\C».
 * 3) An object. It is reduced to case 2 via @see get_class()
 * @used-by dfe_modules_info()
 * @used-by dfe_packages()
 * @used-by \Df\Config\Fieldset::_getHeaderCommentHtml()
 * @param string|object|null $m [optional]
 * @param string|string[]|null $k [optional]
 * @param mixed|null $d [optional]
 * @return string|array(string => mixed)|null
 */
function df_package($m = null, $k = null, $d = null) {
	/** @var array(string => array(string => mixed)) $cache */
	static $cache;
	if (!isset($cache[$m = df_module_name($m)])) {
		$packagePath = df_module_path($m); /** @var string $packagePath */
		// 2017-01-10
		// У модулей «Df_*» общий файл «composer.json», и он расположен  в родительской папке этих модулей.
		if (df_starts_with($m, 'Df_')) {
			$packagePath = dirname($packagePath);
		}
		$filePath = "$packagePath/composer.json"; /** @var string $filePath */
		$cache[$m] = !file_exists($filePath) ? [] : df_json_decode(file_get_contents($filePath));
	}
	return dfak($cache[$m], $k, $d);
}

/**
 * 2017-04-16
 * @used-by df_log_l()
 * @param string|object|null $m [optional]
 * @return string|null
 */
function df_package_name_l($m = null) {return df_last(explode('/', df_package($m, 'name')));}

/**
 * 2016-06-26
 * The method can be used not only for the custom packages,
 * but for the standard Magento packages too.
 * «How to programmatically get an extension's version from its composer.json file?»
 * https://mage2.pro/t/1798
 * 2017-04-10
 * Отныне эта функция всегда берёт свой результат из локального файла composer.json.
 * Имя установочного пакета в качестве $name больше не допускается!
 * @used-by df_sentry()
 * @used-by df_sentry_m() 
 * @used-by dfp_sentry_tags()
 * @used-by \Df\Payment\Method::action()
 * @used-by \Df\Sentry\Client::version()
 * @used-by \Dfe\CheckoutCom\Charge::metaData()
 * @param string|object|null $m [optional]
 * @return string|null
 */
function df_package_version($m = null) {return df_package($m, 'version');}

/**
 * 2017-05-05 It returns an array like [«Dfe_PortalStripe» => [<the package's composer.json as an array>]]].
 * @used-by dfe_portal_plugins()
 * @return array(string => array(string => mixed))
 */
function dfe_packages() {return dfcf(function() {return df_map_r(dfe_modules(), function($m) {return [
	$m, df_package($m)
];});});}