<?php
namespace Df\SampleData\Model;
use Magento\Framework\Component\ComponentRegistrar as R;
use Magento\Framework\Config\Composer\Package;
use Magento\Framework\Filesystem\Directory\Read;
use Magento\Framework\Filesystem\Directory\ReadInterface as IRead;
use stdClass as O;
# 2016-09-03
# «Warning: file_get_contents(vendor/mage2pro/core/<...>/composer.json):
# failed to open stream: No such file or directory in vendor/magento/module-sample-data/Model/Dependency.php on line 109»:
# https://mage2.pro/t/2002
# 2024-06-09
# 1) "Rework `Df\SampleData\Model\Dependency`": https://github.com/mage2pro/core/issues/411
# 2) The «failed to open stream» bug has been fixed since Magento 2.2:
# 2.1) «Ensure composer.json exists»: https://github.com/magento/magento2/commit/6dd36ba2
# 3) The parent implementation looks for `composer.json` in the parent directory of the module since Magento 2.3:
# 3.1) «Look for composer.json in parent of registered module directory for sample data suggestions»:
# https://github.com/magento/magento2/commit/29bc089e
# 3.2) So my fix is not neeeded for Magento ≥ 2.3.
class Dependency extends \Magento\SampleData\Model\Dependency {
	/**
	 * 2020-06-16
	 * @override
	 * @see \Magento\SampleData\Model\Dependency::getSuggestsFromModules()
	 * @used-by \Magento\SampleData\Model\Dependency::getSampleDataPackages()
	 * @throws \Magento\Framework\Exception\FileSystemException
	 */
	protected function getSuggestsFromModules():array {
		$r = []; /** @var array $r */
		foreach (df_component_r()->getPaths(R::MODULE) as $path) {/** @var string $path */
			$package = $this->package($path); /** @var Package $package */
			$suggest = json_decode(json_encode($package->get('suggest')), true);
			if (!empty($suggest)) {
				$r += $suggest;
			}
		}
		return array_merge(df_map(df_modules(), function(string $m):array {return df_package($m, 'suggest', []);}));
	}

	/**
	 * 2020-06-15
	 * The @see \Magento\SampleData\Model\Dependency::getModuleComposerPackage() method became private
	 * since 2017-03-23 by the following commit: https://github.com/magento/magento2/commit/29bc089e
	 * This commit is applied to Magento ≥ 2.3.0.
	 * 2024-06-09
	 * It is identical to @see \Magento\SampleData\Model\Dependency::getModuleComposerPackage()
	 * https://github.com/magento/magento2/blob/2.4.7/app/code/Magento/SampleData/Model/Dependency.php#L109-L134
	 * @used-by self::getSuggestsFromModules()
	 * @throws \Magento\Framework\Exception\FileSystemException
	 */
	private function package(string $path):Package {return df_package_new(
		# 2024-06-09
		# 1) «$modulePath/..» means the parent directory:
		# 		«Also look in parent directory of registered module directory to allow modules to follow the pds/skeleton standard
		# 		and have their source code in a "src" subdirectory of the repository
		# 		see: https://github.com/php-pds/skeleton»
		# https://github.com/magento/magento2/blob/2.4.7/app/code/Magento/SampleData/Model/Dependency.php#L119-L122
		# 2) The parent implementation looks for `composer.json` in the parent directory of the module since Magento 2.3:
		# 2.1) «Look for composer.json in parent of registered module directory for sample data suggestions»:
		# https://github.com/magento/magento2/commit/29bc089e
		# 3) Previously, I had the code:
		# 		private function mage2pro(string $f):string {return false === strpos($f, 'mage2pro') || file_exists($f) ? $f :
		#			preg_replace('#/mage2pro/core/[^/]+/#', '/mage2pro/core/', df_path_n($f))
		#		;}
		# https://github.com/mage2pro/core/blob/11.1.6/SampleData/Model/Dependency.php#L40-L47
		# I do not need it anymore because of «$modulePath/..».
		df_find([$path, "$path/.."], function(string $p):?O {
			$rd = df_fs_rf()->create($p); /** @var IRead|Read $rd */ /** @const string $f */
			return $rd->isExist($f = 'composer.json') && $rd->isReadable($f) ? json_decode($rd->readFile($f)) : null;
		}) ?: new O
	);}
}