<?php
namespace Df\Framework\Upgrade;
use Magento\Framework\Setup\ModuleContextInterface as IModuleContext;
use Magento\Framework\Setup\ModuleDataSetupInterface as IDataSetup;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Setup\Model\ModuleContext;
use Magento\Setup\Module\DataSetup;
// 2016-12-02
/** @see \Df\Sso\Upgrade\Data */
abstract class Data extends \Df\Framework\Upgrade implements UpgradeDataInterface {
	/**
	 * 2016-12-02
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see UpgradeSchemaInterface::upgrade()
	 * @param DataSetup|IDataSetup $setup
	 * @param IModuleContext|ModuleContext $context
	 */
	final function upgrade(IDataSetup $setup, IModuleContext $context) {$this->process($setup, $context);}
}