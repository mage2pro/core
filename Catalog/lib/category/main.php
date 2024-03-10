<?php
use Magento\Catalog\Api\CategoryRepositoryInterface as ICategoryRepository;
use Magento\Catalog\Model\Category as C;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Catalog\Model\Product as P;
use Magento\Quote\Model\Quote\Item as QI;
use Magento\Sales\Model\Order\Item as OI;
use Magento\Store\Api\Data\StoreInterface as IStore;

/**
 * 2019-09-08
 * @see df_product()
 * @used-by df_category_children()
 * @used-by df_category_children_map()
 * @used-by \Sharapov\Cabinetsbay\Block\Category\View::parent3() (https://github.com/cabinetsbay/site/issues/98)
 * @used-by \Wolf\Filter\Block\Navigation::hDropdowns()
 * @used-by \Wolf\Filter\Block\Navigation::selectedPath()
 * @used-by \Wolf\Filter\Controller\Index\Change::execute()
 * @param int|string|C $c
 * @param int|string|null|bool|IStore $s [optional]
 */
function df_category($c, $s = false):C {return $c instanceof C ? $c : df_category_r()->get(
	$c, false === $s ? null : df_store_id(true === $s ? null : $s)
);}

/**
 * 2021-11-30 @deprecated It is unused.
 * @see df_product_id()
 * @param C|int $c
 */
function df_category_id($c):int {return df_int($c instanceof C ? $c->getId() : $c);}

/**
 * 2024-03-10
 * @uses \Magento\Catalog\Model\Category::getLevel() can return a string (e.g., "3").
 * @used-by cb_category_is_top() (https://github.com/cabinetsbay/site/issues/98)
 * @used-by \Sharapov\Cabinetsbay\Block\Category\View::getRootCategoryName() (https://github.com/cabinetsbay/site/issues/98)
 * @used-by \Sharapov\Cabinetsbay\Block\Category\View::level() (https://github.com/cabinetsbay/site/issues/98)
 * @used-by \Sharapov\Cabinetsbay\Block\Category\View::parent3() (https://github.com/cabinetsbay/site/issues/98)
 * @used-by \Sharapov\Cabinetsbay\Block\Product\ListProduct::level() (https://github.com/cabinetsbay/site/issues/98)
 * @used-by app/design/frontend/Cabinetsbay/cabinetsbay_default/Magento_Catalog/templates/product/list.phtml:45 (https://github.com/cabinetsbay/site/issues/98)
 */
function df_category_level(C $c):int {return (int)$c->getLevel();}

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