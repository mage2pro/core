<?php
namespace Df\SampleData\Model;
/**
 * 2016-09-03
 * «Warning: file_get_contents(vendor/mage2pro/core/<...>/composer.json):
 * failed to open stream: No such file or directory in vendor/magento/module-sample-data/Model/Dependency.php on line 109»:
 * https://mage2.pro/t/2002
 * 2024-06-09
 * 1) "Rework `Df\SampleData\Model\Dependency`": https://github.com/mage2pro/core/issues/411
 * 2) The «failed to open stream» bug has been fixed since Magento 2.2:
 * «Ensure composer.json exists»: https://github.com/magento/magento2/commit/6dd36ba2
 * 3) The parent implementation looks for `composer.json` in the parent directory of the module since Magento 2.3:
 * «Look for composer.json in parent of registered module directory for sample data suggestions»:
 * https://github.com/magento/magento2/commit/29bc089e
 * 4) "`df_package()` should look for `composer.json` in the parent directory for all packages (not only `mage2pro/*`),
 * similar to @see \Magento\SampleData\Model\Dependency::getModuleComposerPackage() in Magento ≥ 2.3":
 * https://github.com/mage2pro/core/issues/412
 * 5) Previously, I had the code:
 * 		private function mage2pro(string $f):string {return false === strpos($f, 'mage2pro') || file_exists($f) ? $f :
 *			preg_replace('#/mage2pro/core/[^/]+/#', '/mage2pro/core/', df_path_n($f))
 *		;}
 * https://github.com/mage2pro/core/blob/11.1.6/SampleData/Model/Dependency.php#L40-L47
 * I do not need it anymore because of 4.
 * 6) My fix is not neeeded for Magento ≥ 2.3 because of 3.
 */
class Dependency extends \Magento\SampleData\Model\Dependency {
	/**
	 * 2020-06-16
	 * @override
	 * @see \Magento\SampleData\Model\Dependency::getSuggestsFromModules()
	 * @used-by \Magento\SampleData\Model\Dependency::getSampleDataPackages()
	 * @throws \Magento\Framework\Exception\FileSystemException
	 */
	protected function getSuggestsFromModules():array {return array_merge(df_map(df_modules(), function(string $m):array {return
		df_package($m, 'suggest', [])
	;}));}
}