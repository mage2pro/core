<?php
use Magento\Catalog\Model\ResourceModel\Product\Collection as C;

/**
 * 2019-09-18
 * @used-by vendor/tradefurniturecompany/report/view/frontend/templates/index.phtml:16
 * used-by df_product_c()
 * @used-by \TFC\Image\Command\C3::pc()
 * @used-by \TFC\Image\Command\C3::pcL()
 * @return C
 */
function df_pc() {return df_new_om(C::class);}

/**
 * 2019-09-18
 * 2020-11-23 @deprecated
 * @used-by \BlushMe\Checkout\Block\Extra::items()
 * @return C
 */
function df_product_c() {return df_pc();}