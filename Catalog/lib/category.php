<?php
use Magento\Catalog\Api\CategoryRepositoryInterface as ICategoryRepository;
use Magento\Catalog\Model\Category as C;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Store\Api\Data\StoreInterface as IStore;
/**
 * 2019-09-08
 * @see df_product()
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