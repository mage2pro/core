<?php
namespace Df\Framework\Upgrade;
use Magento\Customer\Api\AddressMetadataInterface as IAddressMetadata;
use Magento\Eav\Api\Data\AttributeGroupInterface as IGroup;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute as A;
use Magento\Eav\Model\Entity\Attribute\Set as _AS;
use Magento\Framework\Setup\ModuleContextInterface as IModuleContext;
use Magento\Framework\Setup\ModuleDataSetupInterface as IDataSetup;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Setup\Model\ModuleContext;
use Magento\Setup\Module\DataSetup;
/**
 * 2016-12-02
 * @see \Df\Customer\Setup\UpgradeData
 * @see \Df\Sso\Upgrade\Data
 * @see \Dfe\IPay88\Setup\UpgradeData
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
	 * @param DataSetup|IDataSetup $setup
	 * @param IModuleContext|ModuleContext $context
	 */
	final function upgrade(IDataSetup $setup, IModuleContext $context) {$this->process($setup, $context);}
	
	/**
	 * 2019-03-05
	 * @see \Df\Sso\Upgrade\Data::attribute()
	 * @used-by \Verdepieno\Core\Setup\UpgradeData::_process()
	 * @param string $name
	 * @param string $label
	 * @param int $ordering [optional]
	 */
	final protected function attributeCA($name, $label, $ordering = 1000) {
        $asId = df_eav_ca()->getDefaultAttributeSetId(); /** @var int $asId */
        $as = df_new_om(_AS::class); /** @var _AS $as */
		df_eav_setup()->addAttribute(IAddressMetadata::ENTITY_TYPE_ADDRESS, $name, [
			'input' => 'text'
			,'label' => $label
			,'position' => $ordering++
			,'required' => false
			,'sort_order' => $ordering
			,'system' => false
			/**
			 * 2019-03-06
			 * `varchar` (a solution without @see \Verdepieno\Core\Setup\UpgradeSchema )
			 * does not work for me.
			 * I guess it is a bug in the Magento 2 Community core.
			 */
			,'type' => 'static'
			,'visible' => true
		]);
		$a = df_eav_config()->getAttribute(IAddressMetadata::ENTITY_TYPE_ADDRESS, $name); /** @var A $a */
		$a->addData([
			IGroup::ATTRIBUTE_SET_ID => $asId
			,'attribute_group_id' => $as->getDefaultGroupId($asId)
			,'used_in_forms' => [
				'adminhtml_customer_address'
				,'customer_address_edit'
				,'customer_register_address'
				,'customer_address'
			]
		]);
		$a->save();
	}
}