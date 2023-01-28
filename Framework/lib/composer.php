<?php
use Magento\Framework\Config\Composer\Package;

/**
 * 2017-01-25
 * @used-by df_context()
 * @used-by df_sentry()
 * @used-by df_sentry_m()
 * @used-by \Df\Sentry\Client::__construct()
 * @used-by \Df\Sentry\Client::getUserAgent()
 * @used-by \Dfe\Klarna\Api\Checkout\V3\UserAgent::__construct()
 */
function df_core_version():string {return dfcf(function() {return df_package_version('Df_Core');});}

/**
 * 2017-01-10
 * The function gets the package's information from the package's `composer.json` file.
 * $m could be:
 * 1) a module name: «A_B»
 * 2) a class name: «A\B\C».
 * 3) an object: it comes down to the case 2 via @see get_class()
 * 4) `null`: it comes down to the case 1 with the «Df_Core» module name.
 * @used-by df_modules_my()
 * @used-by df_package_name_l()
 * @used-by df_package_version()
 * @used-by dfe_modules_info()
 * @used-by dfe_packages()
 * @used-by dfe_portal_module()
 * @used-by \Df\Config\Fieldset::_getHeaderCommentHtml()
 * @param string|object|null $m [optional]
 * @param string|string[] $k [optional]
 * @param mixed|null $d [optional]
 * @return string|array(string => mixed)|null
 */
function df_package($m = null, $k = '', $d = null) {
	static $cache; /** @var array(string => array(string => mixed)) $cache */
	if (!isset($cache[$m = df_module_name($m)])) {
		$packagePath = df_module_path($m); /** @var string $packagePath */
		# 2017-01-10 All `Df_*` modules share the common `composer.json` located in the parent folder.
		if (df_starts_with($m, 'Df_')) {
			$packagePath = dirname($packagePath);
		}
		$filePath = "$packagePath/composer.json"; /** @var string $filePath */
        # 2023-01-28
        # 1) The `composer.json` file can be absent for a module, e.g.:
        # https://github.com/elgentos/magento2-regenerate-catalog-urls/tree/0.2.14/Iazel/RegenProductUrl
        # 2) "«Unable to read the file vendor/elgentos/regenerate-catalog-urls/Iazel/RegenProductUrl/composer.json»
        # on `bin/magento setup:static-content:deploy`": https://github.com/tradefurniturecompany/site/issues/240
		$cache[$m] = df_eta(df_json_decode(df_file_read($filePath, false)));
	}
	return dfa($cache[$m], $k, $d);
}

/**
 * 2020-06-16
 * @used-by \Df\SampleData\Model\Dependency::getModuleComposerPackageParent()
 * @see \Magento\Framework\Config\Composer\Package::__construct()
 */
function df_package_new(stdClass $json):Package {return df_new_om(Package::class, ['json' => $json]);}

/**
 * 2017-04-16
 * 2020-09-24 @deprecated It is unused.
 * @param string|object|null $m [optional]
 * @return string|null
 */
function df_package_name_l($m = null) {return df_last(explode('/', df_package($m, 'name')));}

/**
 * 2016-06-26
 * The method can be used not only for custom packages, but for standard Magento packages too.
 * «How to programmatically get an extension's version from its composer.json file?» https://mage2.pro/t/1798
 * 2017-04-10
 * From now on, the function gets the package's name from the package's `composer.json` file only.
 * A package's name as $m is not allowed anymore.
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
 * @used-by dfe_modules_info()
 * @used-by dfe_portal_plugins()
 * @return array(string => array(string => mixed))
 */
function dfe_packages():array {return dfcf(function() {return df_map_r(dfe_modules(), function($m) {return [
	$m, df_package($m)
];});});}