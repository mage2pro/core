<?php
use Magento\Catalog\Api\CategoryRepositoryInterface as ICategoryRepository;
use Magento\Catalog\Helper\Category as H;
use Magento\Catalog\Model\Category as C;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Catalog\Model\Product as P;
use Magento\Quote\Model\Quote\Item as QI;
use Magento\Sales\Model\Order\Item as OI;
use Magento\Store\Api\Data\StoreInterface as IStore;

/**
 * 2019-09-08
 * @see df_product()
 * @used-by df_category_ancestor_at_level()
 * @used-by df_category_children()
 * @used-by df_category_children_map()
 * @used-by \Wolf\Filter\Block\Navigation::hDropdowns()
 * @used-by \Wolf\Filter\Block\Navigation::selectedPath()
 * @used-by \Wolf\Filter\Controller\Index\Change::execute()
 * @used-by \Sharapov\Cabinetsbay\Block\Category\View::l3p() (https://github.com/cabinetsbay/site/issues/98)
 * @param int|string|C $c
 * @param int|string|null|bool|IStore $s [optional]
 */
function df_category($c, $s = false):C {return $c instanceof C ? $c : df_category_r()->get(
	$c, false === $s ? null : df_store_id(true === $s ? null : $s)
);}

/**
 * 2024-03-14
 * @used-by app/design/frontend/Cabinetsbay/cabinetsbay_default/Magento_Catalog/templates/category/view.phtml (https://github.com/cabinetsbay/site/issues/112)
 * @used-by vendor/cabinetsbay/core/view/frontend/templates/catalog/category/tabs.phtml (https://github.com/cabinetsbay/site/issues/105)
 */
function df_category_h():H {return df_o(H::class);}

/**
 * 2021-11-30 @deprecated It is unused.
 * @see df_product_id()
 * @param C|int $c
 */
function df_category_id($c):int {return df_int($c instanceof C ? $c->getId() : $c);}

/**
 * 2020-02-05
 * @see df_store_names()
 * @used-by \Dfe\Sift\Payload\OQI::p()
 * @param int|string|P|OI|QI $p
 * @param int|string|null|bool|IStore $s [optional]
 * @return string[]
 */
function df_category_names($p, $s = false):array {return df_each(
	df_product($p, $s)->getCategoryCollection()->addAttributeToSelect($k = 'name'), $k
);}

/**
 * 2019-09-08   
 * @see df_product_r()
 * @used-by df_category()
 * @return ICategoryRepository|CategoryRepository
 */
function df_category_r() {return df_o(ICategoryRepository::class);}