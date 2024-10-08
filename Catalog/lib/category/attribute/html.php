<?php
use Magento\Catalog\Model\Category as C;
/**
 * 2024-06-17 "Implement `df_category_att_html()`": https://github.com/mage2pro/core/issues/427
 * @see df_product_att_html()
 * @used-by vendor/cabinetsbay/catalog/view/frontend/templates/category/l3/tabs/overview.phtml (https://github.com/cabinetsbay/catalog/issues/22)
 */
function df_category_att_html(C $c, string $v, string $ac):string {return df_catalog_output()->categoryAttribute($c, $v, $ac);}