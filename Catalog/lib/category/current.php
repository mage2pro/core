<?php
use Magento\Catalog\Model\Category as C;
/**
 * 2024-04-15
 * 2024-04-20 "Implement `df_category_current()`" https://github.com/mage2pro/core/issues/363
 */
function df_category_current():C {return df_catalog_layer()->getCurrentCategory();}