<?php
use Magento\Framework\Composer\ComposerInformation;
/**
 * 2016-06-26
 * @return ComposerInformation;
 */
function df_composer() {return df_o(ComposerInformation::class);}

/**
 * 2016-07-01
 * The method returns a package's information from its composer.json file.
 * «How is @see \Magento\Framework\Composer\ComposerInformation::getInstalledMagentoPackages()
 * implemented and used?» https://mage2.pro/t/1796
 * @param string $name
 * @param $key|null $key [optional]
 * @return array(string => string)|string|null
 */
function df_package($name, $key = null) {
	/** @var array(string => array(string => string)) $packages */
	static $packages;
	if (!$packages) {
		/**
		 * 2016-06-26
		 * A package entry looks like:
			"mage2pro/checkout.com": {
				"name": "mage2pro/checkout.com",
				"type": "magento2-module",
				"version": "1.0.5"
			}
		 */
		$packages = df_composer()->getInstalledMagentoPackages();
	}
	/**
	 * 2016-06-26
	 * We can not use @see dfa_deep() here, because a package name contains the «/» symbol,
	 * e.g.: «mage2pro/amazon-payments».
	 */
	/** @var array(string => string) $result */
	$result = dfa($packages, $name, []);
	return is_null($key) ? $result : dfa($result, $key);
}

/**
 * 2016-06-26
 * The method returns a version only for a custom package,
 * not for a Magento standard package!
 * «How to programmatically get an extension's version from its composer.json file?»
 * https://mage2.pro/t/1798
 * «How is @see \Magento\Framework\Composer\ComposerInformation::getInstalledMagentoPackages()
 * implemented and used?» https://mage2.pro/t/1796
 * @param string $name [optional]
 * @return string|null
 */
function df_package_version($name) {return df_package($name, 'version');}


