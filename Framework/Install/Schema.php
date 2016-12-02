<?php
namespace Df\Framework\Install;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface as IModuleContext;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Setup\Model\ModuleContext;
use Magento\Setup\Module\Setup;
abstract class Schema
	extends \Df\Framework\Install implements InstallSchemaInterface, UpgradeSchemaInterface {
	/**
	 * 2015-10-23
	 * @override
	 * @see InstallSchemaInterface::install()
	 * @param Setup|SchemaSetupInterface $setup
	 * @param IModuleContext|ModuleContext $context
	 * @return void
	 */
	public function install(SchemaSetupInterface $setup, IModuleContext $context) {
		$this->process($setup, $context);
	}

	/**
	 * 2016-08-14
	 * @override
	 * @see UpgradeSchemaInterface::upgrade()
	 * @param Setup|SchemaSetupInterface $setup
	 * @param IModuleContext|ModuleContext $context
	 * @return void
	 */
	public function upgrade(SchemaSetupInterface $setup, IModuleContext $context) {
		$this->process($setup, $context);
	}
}