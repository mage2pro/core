<?php
use Magento\Framework\Composer\ComposerInformation;
/**
 * 2016-06-25
 * @return ComposerInformation;
 */
function df_composer() {return df_o(ComposerInformation::class);}

/**
 * 2016-06-25
 * The method returns an information inly for a custom package,
 * not for Magento standard packages!
 * A package entry looks like:
	"mage2pro/checkout.com": {
		"name": "mage2pro/checkout.com",
		"type": "magento2-module",
		"version": "1.0.5"
	}
 * «How is @see \Magento\Framework\Composer\ComposerInformation::getInstalledMagentoPackages()
 * implemented and used?» https://mage2.pro/t/1796
 * @param string|null $name [optional]
 * @return array(string => string)|array(string => array(string => string))|null
 */
function df_package_custom($name = null) {
	/** @var array(string => array(string => string)) $result */
	static $result;
	if (!$result) {
		$result = df_composer()->getInstalledMagentoPackages();
	}
	return !$name ? $result : dfa($result, $name);
}

