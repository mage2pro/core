<?php
/**
 * 2017-01-10
 * Эта функция  считывает информацию из локального файла «composer.json» того модуля,
 * которому принадлежит класс $c.
 * Функция допускает в качестве $m:
 * 1) Имя модуля. Например: «A_B».
 * 2) Имя класса. Например: «A\B\C».
 * 3) Объект. Сводится к случаю 2 посредством @see get_class()
 * @used-by df_modules()
 * @used-by \Df\Config\Fieldset::_getHeaderCommentHtml()
 * @param string|object $m
 * @param string|string[]|null $k [optional]
 * @param mixed|null $v [optional]
 * @return string|array(string => mixed)|null
 */
function df_package($m, $k = null, $v = null) {
	/** @var array(string => array(string => mixed)) $cache */
	static $cache;
	if (!isset($cache[$m = df_module_name($m)])) {
		/** @var string $packagePath */
		$packagePath = df_module_path($m);
		// 2017-01-10
		// У модулей «Df_*» общий файл «composer.json», и он расположен
		// в родительской папке этих модулей.
		if (df_starts_with($m, 'Df_')) {
			$packagePath = dirname($packagePath);
		}
		/** @var string $filePath */
		$filePath = "$packagePath/composer.json";
		$cache[$m] = !file_exists($filePath) ? [] : df_json_decode(file_get_contents($filePath));
	}
	return dfak($cache[$m], $k, $v);
}

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
 * @param string|object $name [optional]
 * @return string|null
 */
function df_package_version($name) {return df_package($name, 'version');}