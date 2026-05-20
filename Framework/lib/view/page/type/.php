<?php
/**
 * 2018-11-23
 * @used-by Frugue\Core\Plugin\Framework\App\PageCache\Kernel()
 * @used-by vendor/ngp/core/view/frontend/templates/Magento/Theme/html/header/logo.phtml (https://github.com/national-glass-partitions/core/issues/7)
 * @used-by Magento\RequireJs\Model\FileManager::createBundleJsPool() (Frugue)
 */
function df_is_home():bool {return df_handle('cms_index_index');}

/**
 * 2017-10-15
 * 2017-12-04
 * The previous code was:
 * 		df_handle('adminhtml_system_config_edit')
 * It is incorrect, because:
 * 1) It does not take into account the `admin/system_config/save` action.
 * 2) We do not have any layout handles yet in a @see \Df\Config\Backend::dfSaveAfter() handler:
 * e.g., in the @see \Dfe\Moip\Backend\Enable::dfSaveAfter() handler.
 * So the following code will not help us:
 * 		df_handle_prefix('adminhtml_system_config_')
 * It can be related to the following Moip issue:
 * "«Please set your Moip private key in the Magento backend» even if the Moip private key is set"
 * https://github.com/mage2pro/moip/issues/22
 * @used-by Df\Config\Settings::scope()
 */
function df_is_system_config():bool {return df_action_prefix('adminhtml_system_config');}