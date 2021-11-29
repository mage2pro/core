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
 * @used-by \Wolf\Filter\Block\Navigation::hDropdowns()
 * @used-by \Wolf\Filter\Block\Navigation::selectedPath()
 * @used-by \Wolf\Filter\Controller\Index\Change::execute()
 * @param int|string|C $c
 * @param int|string|null|bool|IStore $s [optional]
 * @return C
 */
function df_category($c, $s = false) {return $c instanceof C ? $c : df_category_r()->get(
	$c, false === $s ? null : df_store_id(true === $s ? null : $s)
);}

/**
 * 2019-09-08   
 * @see df_product_r()
 * @used-by df_category()
 * @return ICategoryRepository|CategoryRepository
 */
function df_category_r() {return df_o(ICategoryRepository::class);}

/**
 * 2020-02-05
 * @see df_store_names()
 * @used-by \Dfe\Sift\Payload\OQI::p()
 * @param int|string|P|OI|QI $p
 * @param int|string|null|bool|IStore $s [optional]
 * @return string[]
 */
function df_category_names($p, $s = false) {return df_each(
	df_product($p, $s)->getCategoryCollection()->addAttributeToSelect($k = 'name'), $k
);}