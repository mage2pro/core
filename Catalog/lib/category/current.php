<?php
use Magento\Catalog\Model\Category as C;
/**
 * 2024-04-15
 * 2024-04-20
 * 1) "Implement `df_category_current()`" https://github.com/mage2pro/core/issues/363
 * 2) https://stackoverflow.com/a/46414822
 * @used-by df_category_level()
 * @used-by vendor/cabinetsbay/catalog/view/frontend/templates/products.phtml (https://github.com/cabinetsbay/catalog/issues/38)
 */
function df_category_current():C {return df_catalog_layer()->getCurrentCategory();}