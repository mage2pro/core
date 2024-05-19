<?php
use Magento\Catalog\Model\Category as C;

/**
 * 2024-03-10
 * "The result of `Magento\Catalog\Model\Category::getParentCategories()` always includes the caller":
 * https://mage2.pro/t/6429
 * @used-by \Sharapov\Cabinetsbay\Block\Category\View::l3() (https://github.com/cabinetsbay/site/issues/98)
 * @return C|null
 */
function df_category_ancestor_at_level(C $c, int $l) {return $l > ($l2 = df_category_level($c)) ? null : (
	$l === $l2 ? $c : df_category($c->getPathIds()[$l])
);}

/**
 * 2024-03-10
 * 1) @uses \Magento\Catalog\Model\Category::getLevel() can return a string (e.g., "3").
 * 2.1) Level 0: «Root Catalog» (undeletable, hidden in UI).
 * 2.2) Level 1: «Default Category» (undeletable, can be renamed).
 * @used-by cb_category_is_l2() (https://github.com/cabinetsbay/site/issues/98)
 * @used-by cb_category_is_l3() (https://github.com/cabinetsbay/site/issues/98)
 * @used-by df_category_ancestor_at_level()
 * @used-by \CabinetsBay\Catalog\Observer\LayoutLoadBefore::execute() (https://github.com/cabinetsbay/catalog/issues/3)
 * @used-by \Sharapov\Cabinetsbay\Block\Category\View::level() (https://github.com/cabinetsbay/site/issues/98)
 * @used-by \Sharapov\Cabinetsbay\Block\Product\ListProduct::level() (https://github.com/cabinetsbay/site/issues/98)
 * @used-by app/design/frontend/Cabinetsbay/cabinetsbay_default/Magento_Catalog/templates/product/list.phtml:45 (https://github.com/cabinetsbay/site/issues/98)
 */
function df_category_level(C $c = null):int {return (int)($c ?: df_category_current())->getLevel();}