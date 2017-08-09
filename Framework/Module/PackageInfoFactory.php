<?php
namespace Df\Framework\Module;
use Df\Framework\Module\Dir\Reader;
use Magento\Framework\Module\FullModuleList;
use Magento\Framework\Module\PackageInfo;
/**
 * 2017-07-26
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 * The purpose of this class is to fix the issue:
 * «bin/magento module:enable --all»: «The file "/composer.json" doesn't exist»
 * https://github.com/mage2pro/stripe/issues/8
 * https://mage2.pro/t/4198
 */
class PackageInfoFactory extends \Magento\Framework\Module\PackageInfoFactory {
	/**
	 * 2017-07-26
	 * 2017-08-09
	 * We override the parent's method to use @see \Df\Framework\Module\Dir\Reader
	 * instead of @see \Magento\Framework\Module\Dir\Reader
	 * @override
	 * @see \Magento\Framework\Module\PackageInfoFactory::create():
	 *		public function create() {
	 *			$fullModuleList = $this->objectManager->create(\Magento\Framework\Module\FullModuleList::class);
	 *			$reader = $this->objectManager->create(
	 *				\Magento\Framework\Module\Dir\Reader::class,
	 *				['moduleList' => $fullModuleList]
	 *			);
	 *			return $this->objectManager->create(
	 *				\Magento\Framework\Module\PackageInfo::class, ['reader' => $reader]
	 *			);
	 *		} 
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.8/lib/internal/Magento/Framework/Module/PackageInfoFactory.php#L30-L43
	 * @used-by \Magento\Framework\Module\DependencyChecker::__construct():
	 * 		$this->packageInfo = $packageInfoFactory->create();
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.8/lib/internal/Magento/Framework/Module/DependencyChecker.php#L41-L53
	 * @return PackageInfo
	 */
    function create() {$om = $this->objectManager; return $om->create(PackageInfo::class, [
    	'reader' => $om->create(Reader::class, ['moduleList' => $om->create(FullModuleList::class)])
	]);}
}

