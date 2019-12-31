<?php
use Magento\Framework\Component\ComponentRegistrar as R;

/**         
 * 2019-12-31
 * @used-by df_lib_path()
 * @return R
 */
function df_component_r() {return df_o(R::class);}

/**
 * 2019-12-31
 * It returns the fill filesystem path of the Magento Framework, e.g.:
 * «C:/work/clients/royalwholesalecandy.com-2019-12-08/code/vendor/magento/framework»
 * or «C:/work/clients/royalwholesalecandy.com-2019-12-08/code/lib/internal/magento/framework»
 * @used-by df_module_dir()
 * @return string
 */
function df_framework_path() {return df_lib_path('magento/framework');}

/**
 * 2019-12-31
 * It returns the fill filesystem path of a library, e.g.:
 * «C:/work/clients/royalwholesalecandy.com-2019-12-08/code/vendor/magento/framework»
 * or «C:/work/clients/royalwholesalecandy.com-2019-12-08/code/lib/internal/magento/framework»
 * @used-by df_framework_path()
 * @param string $l
 * @return string
 */
function df_lib_path($l) {return df_component_r()->getPath(R::LIBRARY, $l);}