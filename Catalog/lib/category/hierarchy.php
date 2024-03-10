<?php
use Magento\Catalog\Model\Category as C;

/**
 * 2024-03-10
 * 1) @uses \Magento\Catalog\Model\Category::getLevel() can return a string (e.g., "3").
 * 2.1) Level 0: «Root Catalog» (undeletable, hidden in UI).
 * 2.2) Level 1: «Default Category» (undeletable, can be renamed).
 * @used-by cb_category_is_l2() (https://github.com/cabinetsbay/site/issues/98)
 * @used-by cb_category_is_l3() (https://github.com/cabinetsbay/site/issues/98)
 * @used-by \Sharapov\Cabinetsbay\Block\Category\View::getRootCategoryName() (https://github.com/cabinetsbay/site/issues/98)
 * @used-by \Sharapov\Cabinetsbay\Block\Category\View::level() (https://github.com/cabinetsbay/site/issues/98)
 * @used-by \Sharapov\Cabinetsbay\Block\Category\View::l3() (https://github.com/cabinetsbay/site/issues/98)
 * @used-by \Sharapov\Cabinetsbay\Block\Product\ListProduct::level() (https://github.com/cabinetsbay/site/issues/98)
 * @used-by app/design/frontend/Cabinetsbay/cabinetsbay_default/Magento_Catalog/templates/product/list.phtml:45 (https://github.com/cabinetsbay/site/issues/98)
 */
function df_category_level(C $c):int {return (int)$c->getLevel();}