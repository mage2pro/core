<?php
use Magento\Framework\Setup\ModuleDataSetupInterface as ISetup;
use Magento\Setup\Module\DataSetup as Setup;
/**
 * 2018-01-20
 * @used-by df_uninstall()
 * @return ISetup|Setup
 */
function df_setup() {return df_o(ISetup::class);}

/**              
 * 2018-01-20    
 * @used-by \Df\API\Setup\UpgradeSchema::_process()
 * @used-by \Df\OAuth\Setup\UpgradeSchema::_process()
 * @see \Magento\Setup\Model\ModuleRegistryUninstaller::removeModulesFromDb()
 * @see \Magento\Setup\Model\ModuleRegistryUninstaller::removeModulesFromDeploymentConfig()
 * @param string $m
 */
function df_uninstall($m) {df_setup()->deleteTableRow('setup_module', 'module', $m);}