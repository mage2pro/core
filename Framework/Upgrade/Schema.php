<?php
namespace Df\Framework\Upgrade;
use Magento\Framework\Setup\ModuleContextInterface as IModuleContext;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Setup\Model\ModuleContext;
use Magento\Setup\Module\Setup;
/**
 * 2016-08-14
 * @see \Df\Customer\Setup\UpgradeSchema
 * @see \Df\Sales\Setup\Schema
 * @see \Df\Sso\Upgrade\Schema
 * @see \Dfe\Markdown\Setup\UpgradeSchema
 */
abstract class Schema extends \Df\Framework\Upgrade implements UpgradeSchemaInterface {
	/**
	 * 2016-08-14
	 * @override
	 * @see UpgradeSchemaInterface::upgrade()
	 * @param Setup|SchemaSetupInterface $setup
	 * @param IModuleContext|ModuleContext $context
	 */
	function upgrade(SchemaSetupInterface $setup, IModuleContext $context) {
		$this->process($setup, $context);
	}
}