<?php
namespace Df\SampleData\Model;
use Magento\Framework\Component\ComponentRegistrar as R;
use Magento\Framework\Config\Composer\Package;
use Magento\Framework\Filesystem\Directory\Read;
use Magento\Framework\Filesystem\Directory\ReadInterface as IRead;
# 2016-09-03
# Для устранения сбоя https://mage2.pro/t/2002
# «Warning: file_get_contents(vendor/mage2pro/core/<...>/composer.json):
# failed to open stream: No such file or directory in vendor/magento/module-sample-data/Model/Dependency.php on line 109»
class Dependency extends \Magento\SampleData\Model\Dependency {
	/**
	 * 2020-06-16
	 * @override
	 * @see \Magento\SampleData\Model\Dependency::getSuggestsFromModules()
	 * @used-by \Magento\SampleData\Model\Dependency::getSampleDataPackages()
	 * @throws \Magento\Framework\Exception\FileSystemException
	 */
	protected function getSuggestsFromModules():array {
		$suggests = [];
		foreach (df_component_r()->getPaths(R::MODULE) as $moduleDir) {
			$package = $this->getModuleComposerPackageMy($moduleDir);
			$suggest = json_decode(json_encode($package->get('suggest')), true);
			if (!empty($suggest)) {
				$suggests += $suggest;
			}
		}
		return $suggests;
	}

	/**
	 * 2020-06-16
	 * 2024-06-09
	 * It is identical to @see \Magento\SampleData\Model\Dependency::getModuleComposerPackage()
	 * https://github.com/magento/magento2/blob/2.4.7/app/code/Magento/SampleData/Model/Dependency.php#L109-L134
	 * @used-by self::getModuleComposerPackageMy()
	 *
	 * @throws \Magento\Framework\Exception\FileSystemException
	 */
	private function getModuleComposerPackageParent(string $modulePath):Package {
		$r = null; /** @var Package $r */
		# 2024-06-09
		# «$modulePath/..» means the parent directory:
		# 		«Also look in parent directory of registered module directory to allow modules to follow the pds/skeleton standard
		# 		and have their source code in a "src" subdirectory of the repository
		# 		see: https://github.com/php-pds/skeleton»
		# https://github.com/magento/magento2/blob/2.4.7/app/code/Magento/SampleData/Model/Dependency.php#L119-L122
		$f = 'composer.json'; /** @const string $f */
		foreach ([$modulePath, "$modulePath/.."] as $p) /** @var string $p */
			$rd = df_fs_rf()->create($p); {/** @var IRead|Read $rd */
			if ($rd->isExist($f) && $rd->isReadable($f)) {
				$r = df_package_new(json_decode($rd->readFile($f)));
				break;
			}
		}
		return $r ?: df_package_new(new \stdClass);
	}

	/**
	 * 2016-09-03 «vendor/mage2pro/core/Backend/composer.json» => «vendor/mage2pro/core/composer.json»
	 * 2020-06-15
	 * The @see \Magento\SampleData\Model\Dependency::getModuleComposerPackage() method became private
	 * since 2017-03-23 by the following commit: https://github.com/magento/magento2/commit/29bc089e
	 * This commit is applied to Magento ≥ 2.3.0.
	 * @see \Magento\SampleData\Model\Dependency::getModuleComposerPackage()
	 * @used-by self::getSuggestsFromModules()
	 */
	private function getModuleComposerPackageMy(string $f):Package {return $this->getModuleComposerPackageParent(
		false === strpos($f, 'mage2pro') || file_exists($f) ? $f : preg_replace(
			'#/mage2pro/core/[^/]+/#', '/mage2pro/core/', df_path_n($f)
		)
	);}
}