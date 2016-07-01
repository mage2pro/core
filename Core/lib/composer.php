<?php
use Composer\Package\CompletePackage as CP;
use Composer\Package\Package as P;
use Composer\Package\PackageInterface as IP;
use Composer\Repository\ArrayRepository;
use Composer\Repository\BaseRepository;
use Composer\Repository\ComposerRepository;
use Composer\Repository\RepositoryInterface;
use Df\Framework\Composer\ComposerInformation as DCI;
use Magento\Framework\Composer\ComposerInformation as CI;
/**
 * 2016-07-01
 * @return DCI;
 */
function df_composer() {return df_o(DCI::class);}

/**
 * 2016-07-01
 * @return RepositoryInterface|ArrayRepository|BaseRepository|ComposerRepository
 */
function df_composer_repository_l() {return df_composer()->locker()->getLockedRepository();}

/**
 * 2016-06-26
 * @return CI;
 */
function df_composer_m() {return df_o(CI::class);}

/**
 * 2016-07-01
 * The method returns a package's information from its composer.json file.
 * The method can be used not only for the custom packages,
 * but for the standard Magento packages too.
 * «How is @see \Magento\Framework\Composer\ComposerInformation::getInstalledMagentoPackages()
 * implemented and used?» https://mage2.pro/t/1796
 * @param string $name
 * @return CP|P|IP|null
 */
function df_package($name) {
	/** @var array(string => P|IP) $packages */
	static $packages;
	if (!isset($packages[$name])) {
		$packages[$name] = df_n_set(df_composer_repository_l()->findPackage($name, '*'));
	}
	return df_n_get($packages[$name]);
}

/**
 * 2016-06-26
 * The method can be used not only for the custom packages,
 * but for the standard Magento packages too.
 * «How to programmatically get an extension's version from its composer.json file?»
 * https://mage2.pro/t/1798
 * @param string $name [optional]
 * @return string|null
 */
function df_package_version($name) {
	/** @var P|IP|null $package */
	$package = df_package($name);
	/**
	 * 2016-07-01
	 * By analogy with
	 * @see \Magento\Framework\Composer\ComposerInformation::getInstalledMagentoPackages()
	 * https://mage2.pro/t/1796
	 */
	return !$package ? null : $package->getPrettyVersion();
}


