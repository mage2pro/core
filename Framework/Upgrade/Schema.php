<?php
namespace Df\Framework\Upgrade;
use Magento\Framework\Setup\ModuleContextInterface as IModuleContext;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Setup\Model\ModuleContext;
use Magento\Setup\Module\Setup;
abstract class Schema extends \Df\Framework\Upgrade implements UpgradeSchemaInterface {
	/**
	 * 2016-08-14
	 * @override
	 * @see UpgradeSchemaInterface::upgrade()
	 * @param Setup|SchemaSetupInterface $setup
	 * @param IModuleContext|ModuleContext $context
	 * @return void
	 */
	function upgrade(SchemaSetupInterface $setup, IModuleContext $context) {
		$this->process($setup, $context);
	}
}