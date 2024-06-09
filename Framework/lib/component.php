<?php
use Magento\Framework\Component\ComponentRegistrar as R;

/**         
 * 2019-12-31
 * @used-by df_lib_path()
 */
function df_component_r():R {return df_o(R::class);}

/**
 * 2019-12-31
 * It returns the fill filesystem path of the Magento Framework, e.g.:
 * «C:/work/clients/royalwholesalecandy.com-2019-12-08/code/vendor/magento/framework»
 * or «C:/work/clients/royalwholesalecandy.com-2019-12-08/code/lib/internal/magento/framework»
 * @used-by df_module_dir()
 */
function df_framework_path():string {return df_lib_path('magento/framework');}

/**
 * 2019-12-31
 * It returns the fill filesystem path of a library, e.g.:
 * «C:/work/clients/royalwholesalecandy.com-2019-12-08/code/vendor/magento/framework»
 * or «C:/work/clients/royalwholesalecandy.com-2019-12-08/code/lib/internal/magento/framework»
 * @used-by df_framework_path()
 */
function df_lib_path(string $l):string {return df_component_r()->getPath(R::LIBRARY, $l);}