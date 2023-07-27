<?php
use Composer\Autoload\ClassLoader as CL;
use Composer\InstalledVersions as IV;
use Composer\Package\RootPackage as Root;
use Magento\Framework\App\ProductMetadata as Metadata;
use Magento\Framework\App\ProductMetadataInterface as IMetadata;

/**
 * 2016-06-25 https://mage2.pro/t/543
 * 2018-04-17
 * 1) «Magento 2.3 has removed its version information from the `composer.json` files since 2018-04-05»:
 * https://mage2.pro/t/5480
 * 2) Now Magento 2.3 (installed with Git) returns the «dev-2.3-develop» string from the
 * @see \Magento\Framework\App\ProductMetadata::getVersion() method.
 * 2023-07-16
 * 1) @see \Magento\Framework\App\ProductMetadata::getVersion() has stopped working correctly for Magento installed via Git:
 * https://github.com/mage2pro/core/issues/229
 * 2) «Script error for "Magento_Ui/js/lib/ko/template/renderer"»: https://github.com/mage2pro/core/issues/228
 * @used-by df_context()
 * @used-by df_magento_version_ge()
 * @used-by df_sentry()
 * @used-by df_sentry_m()
 * @used-by \Df\Qa\Trace\Frame::url()
 * @used-by \Dfe\CheckoutCom\Charge::pMetadata()
 * @used-by \Dfe\Klarna\Api\Checkout\V3\UserAgent::__construct()
 */
function df_magento_version():string {return dfcf(function() {
	/** @var string $r */
	if (
		/**
		 * 2023-07-21
		 * "\Magento\Framework\App\ProductMetadata::getVersion() returns «1.0.0+no-version-set»
		 * in Magento2.4.7-beta1 installed via Git": https://github.com/mage2pro/core/issues/229
		 */
		ROOT::DEFAULT_PRETTY_VERSION === ($r = df_magento_version_m()->getVersion())
		/**
		 * 2023-07-23
		 * 1) @uses \Composer\InstalledVersions::getRootPackage():
		 * 		$installed = self::getInstalled();
		 * 		return $installed[0]['root'];
		 * 2) @see \Composer\InstalledVersions::getInstalled()
		 * returns `[null]` in tradefurniturecompany.co.uk (Windows, PHP 7.4).
		 * 3) It is because of the code:
		 * 		self::$canGetVendors = method_exists('Composer\Autoload\ClassLoader', 'getRegisteredLoaders');
		 * 4) The @see \Composer\Autoload\ClassLoader presents in Magento 2.3.7 in 2 different versions:
		 * 4.1) vendor/composer/composer/src/Composer/Autoload/ClassLoader.php
		 * 4.2) vendor/composer/ClassLoader.php
		 * 5) The `vendor/composer/ClassLoader.php` class is outdated
		 * and does not have the @see \Composer\Autoload\ClassLoader::getRegisteredLoaders() method.
		 * 6) That is why @see \Composer\InstalledVersions::getInstalled() returns `[null].
		 * 7) It leads to the failure:
		 * «Trying to access array offset on value of type null
		 * in vendor/composer/composer/src/Composer/InstalledVersions.php on line 198»:
		 * https://github.com/mage2pro/core/issues/243
		 */
		&& method_exists(CL::class, 'getRegisteredLoaders')
	) {
		/**
		 * 2023-07-16
		 * 1) https://getcomposer.org/doc/07-runtime.md#installed-versions
		 * 2) @uses \Composer\InstalledVersions::getRootPackage() returns:
		 *	{
		 *		"aliases": [],
		 *		"dev": true,
		 *		"install_path": "C:\\work\\clients\\m\\latest\\code\\vendor\\composer/../../",
		 *		"name": "magento/magento2ce",
		 *		"pretty_version": "dev-2.4-develop",
		 *		"reference": "1bdf9dfaf502ab38f5174f33b05c0690f67bf572",
		 *		"type": "project",
		 *		"version": "dev-2.4-develop"
		 *	}
		 */
		$r = dfa(IV::getRootPackage(), 'pretty_version');
	}
	return df_trim_text_left($r, 'dev-');
});}

/**
 * 2016-08-24
 * @used-by \Df\Intl\Js::_toHtml()
 */
function df_magento_version_ge(string $v):bool {return version_compare(df_magento_version(), $v, 'ge');}

/**
 * 2016-06-25
 * @used-by df_magento_version()
 * @return IMetadata|Metadata
 */
function df_magento_version_m() {return df_o(IMetadata::class);}

/**
 * 2017-05-13 https://mage2.pro/t/2615
 * 2022-10-14 @deprecated It is unused. And it is slow.
 */
function df_magento_version_remote(string $url):string {return dfcf(function($url) {return df_try(function() use($url) {
	/** @var string[] $a */
	$a = df_explode_space(df_string_clean(df_trim_text_left(df_contents("$url/magento_version"), 'Magento/'), '(', ')'));
	return 2 !== count($a) ? [] : array_combine(['version', 'edition'], $a);
});}, [df_trim_ds_right($url)]);}
