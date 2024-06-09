<?php
use Magento\Framework\Module\Manager as MM;
use Magento\Framework\Module\ModuleList as ML;
use Magento\Framework\Module\ModuleListInterface as IML;

/**
 * 2017-04-01
 * @used-by df_modules_my()
 * @used-by df_modules_p()
 * @return IML|ML
 */
function df_module_list() {return df_o(IML::class);}

/**
 * 2019-11-21
 * @used-by df_module_enabled()
 */
function df_module_m():MM {return df_o(MM::class);}