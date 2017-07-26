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
	 * @override
	 * @see \Magento\Framework\Module\PackageInfoFactory::create()
	 * @used-by \Magento\Framework\Module\DependencyChecker::__construct()
	 * @return PackageInfo
	 */
    function create() {$om = $this->objectManager; return $om->create(PackageInfo::class, [
    	'reader' => $om->create(Reader::class, ['moduleList' => $om->create(FullModuleList::class)])
	]);}
}

