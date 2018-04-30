<?php
namespace Df\Framework\Upgrade;
use Magento\Framework\Setup\ModuleContextInterface as IModuleContext;
use Magento\Framework\Setup\SchemaSetupInterface as ISchemaSetup;
use Magento\Framework\Setup\UpgradeSchemaInterface as IUpgradeSchema;
use Magento\Setup\Model\ModuleContext;
use Magento\Setup\Module\Setup;
/**
 * 2016-08-14
 * @see \Df\API\Setup\UpgradeSchema
 * @see \Df\Customer\Setup\UpgradeSchema
 * @see \Df\OAuth\Setup\UpgradeSchema
 * @see \Df\Sales\Setup\Schema
 * @see \Df\Sales\Setup\UpgradeSchema
 * @see \Df\Sso\Upgrade\Schema
 * @see \Dfe\Markdown\Setup\UpgradeSchema
 */
abstract class Schema extends \Df\Framework\Upgrade implements IUpgradeSchema {
	/**
	 * 2016-08-14
	 * @override
	 * @see IUpgradeSchema::upgrade()
	 * @used-by \Magento\Setup\Model\Installer::handleDBSchemaData():
	 *		if ($currentVersion !== '') {
	 *			$status = version_compare($configVer, $currentVersion);
	 *			if ($status == \Magento\Framework\Setup\ModuleDataSetupInterface::VERSION_COMPARE_GREATER) {
	 *				$upgrader = $this->getSchemaDataHandler($moduleName, $upgradeType);
	 *				if ($upgrader) {
	 *					$this->log->logInline("Upgrading $type.. ");
	 *					$upgrader->upgrade($setup, $moduleContextList[$moduleName]);
	 *				}
	 *				if ($type === 'schema') {
 	 *					$resource->setDbVersion($moduleName, $configVer);
	 *				}
	 *				elseif ($type === 'data') {
	 *					$resource->setDataVersion($moduleName, $configVer);
	 *				}
	 *			}
	 *		}
	 *		elseif ($configVer) {
	 *			$installer = $this->getSchemaDataHandler($moduleName, $installType);
	 *			if ($installer) {
	 *				$this->log->logInline("Installing $type... ");
	 *				$installer->install($setup, $moduleContextList[$moduleName]);
	 *			}
	 *			$upgrader = $this->getSchemaDataHandler($moduleName, $upgradeType);
	 *			if ($upgrader) {
	 *				$this->log->logInline("Upgrading $type... ");
	 *				$upgrader->upgrade($setup, $moduleContextList[$moduleName]);
	 *			}
	 *			if ($type === 'schema') {
	 *				$resource->setDbVersion($moduleName, $configVer);
	 *			}
	 *			elseif ($type === 'data') {
	 *				$resource->setDataVersion($moduleName, $configVer);
	 *			}
	 *		}
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.6/setup/src/Magento/Setup/Model/Installer.php#L844-L881
	 * @param Setup|ISchemaSetup $setup
	 * @param IModuleContext|ModuleContext $context
	 */
	function upgrade(ISchemaSetup $setup, IModuleContext $context) {$this->process($setup, $context);}
}