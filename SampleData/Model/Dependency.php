<?php
namespace Df\SampleData\Model;
/**
 * 2016-09-03
 * Для устранения сбоя https://mage2.pro/t/2002
 * «Warning: file_get_contents(vendor/mage2pro/core/<...>/composer.json):
 * failed to open stream: No such file or directory
 * in vendor/magento/module-sample-data/Model/ Dependency.php on line 109»
 */
class Dependency extends \Magento\SampleData\Model\Dependency {
	/**
	 * 2016-09-03
	 * «vendor/mage2pro/core/Backend/composer.json» => «vendor/mage2pro/core/composer.json»
	 * @override
	 * @see \Magento\SampleData\Model\Dependency::getModuleComposerPackage()
	 * @used-by \Magento\SampleData\Model\Dependency::getSuggestsFromModules()
	 * @param string $f
	 * @return \Magento\Framework\Config\Composer\Package
	 */
	protected function getModuleComposerPackage($f) {return parent::getModuleComposerPackage(
		false === strpos($f, 'mage2pro') || file_exists($f) ? $f : preg_replace(
			'#/mage2pro/core/[^/]+/#', '/mage2pro/core/', df_path_n($f)
		)
	);}
}