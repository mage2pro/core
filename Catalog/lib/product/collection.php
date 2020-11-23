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
 * 2020-11-23
 * 1) "Add an ability to preserve disabled products in a collection
 * despite of the `cataloginventory/options/show_out_of_stock` option's value": https://github.com/mage2pro/core/issues/148
 * 2) Currently, it is unused here, but used in Justuno.
 * @param C $c
 * @return C
 */
function df_pc_preserve_disabled(C $c) {return $c->setFlag('mage2pro_preserve_disabled');}

/**
 * 2019-09-18
 * 2020-11-23 @deprecated
 * @used-by \BlushMe\Checkout\Block\Extra::items()
 * @return C
 */
function df_product_c() {return df_pc();}