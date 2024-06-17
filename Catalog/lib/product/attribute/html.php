<?php
use Magento\Catalog\Model\Product as P;
/**
 * 2024-06-17 "Implement `df_product_att_html()`": https://github.com/mage2pro/core/issues/426
 * @see df_category_att_html()
 * @see df_product_att_val()
 * @used-by vendor/cabinetsbay/catalog/view/frontend/templates/products.phtml (https://github.com/cabinetsbay/catalog/issues/38)
 */
function df_product_att_html(P $p, string $v, string $ac):string {return df_catalog_output()->productAttribute($p, $v, $ac);}