<?php
namespace Df\Framework\Upgrade;
use Magento\Framework\Setup\ModuleContextInterface as IModuleContext;
use Magento\Framework\Setup\SchemaSetupInterface as ISchemaSetup;
use Magento\Framework\Setup\UpgradeSchemaInterface as IUpgradeSchema;
use Magento\Setup\Model\ModuleContext;
use Magento\Setup\Module\Setup;
/**
 * 2016-08-14
 * @see \Df\Customer\Setup\UpgradeSchema
 * @see \Df\Sales\Setup\Schema
 * @see \Df\Sso\Upgrade\Schema
 * @see \Dfe\Markdown\Setup\UpgradeSchema
 */
abstract class Schema extends \Df\Framework\Upgrade implements IUpgradeSchema {
	/**
	 * 2016-08-14
	 * @override
	 * @see IUpgradeSchema::upgrade()
	 * @param Setup|ISchemaSetup $setup
	 * @param IModuleContext|ModuleContext $context
	 */
	function upgrade(ISchemaSetup $setup, IModuleContext $context) {$this->process($setup, $context);}
}