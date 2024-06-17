<?php
use Magento\Catalog\Api\CategoryRepositoryInterface as ICategoryRepository;
use Magento\Catalog\Helper\Category as H;
use Magento\Catalog\Model\CategoryRepository;

/**
 * 2024-03-14
 * @used-by vendor/cabinetsbay/catalog/view/frontend/templates/category/view.phtml (https://github.com/cabinetsbay/catalog/issues/18)
 */
function df_category_h():H {return df_o(H::class);}

/**
 * 2019-09-08
 * @see df_product_r()
 * @used-by df_category()
 * @return ICategoryRepository|CategoryRepository
 */
function df_category_r() {return df_o(ICategoryRepository::class);}