<?php
namespace Df\Framework\Upgrade;
use Magento\Framework\Setup\ModuleContextInterface as IModuleContext;
use Magento\Framework\Setup\ModuleDataSetupInterface as IDataSetup;
use Magento\Framework\Setup\UpgradeDataInterface;
/**
 * 2016-12-02
 * @see \Df\Customer\Setup\UpgradeData
 * @see \Df\Sso\Upgrade\Data
 * @see \Dfe\IPay88\Setup\UpgradeData
 * @see \KingPalm\B2B\Setup\UpgradeData
 * @see \Sharapov\Cabinetsbay\Setup\UpgradeData (https://github.com/cabinetsbay/site/issues/98)
 * @see \Verdepieno\Core\Setup\UpgradeData
 */
abstract class Data extends \Df\Framework\Upgrade implements UpgradeDataInterface {
	/**
	 * 2016-12-02
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see UpgradeSchemaInterface::upgrade()
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
	 */
	final function upgrade(IDataSetup $setup, IModuleContext $context):void {$this->process($setup, $context);}
}