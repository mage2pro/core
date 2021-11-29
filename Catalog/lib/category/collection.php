<?php
use Magento\Catalog\Model\Category as C;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CC;
/**
 * 2021-11-30
 * @see df_pc()
 * @see df_product_c()
 * @used-by df_category_children()
 * @return CC
 */
function df_category_c() {return df_new_om(CC::class);}

/**
 * 2021-11-30
 * https://github.com/JustunoCom/m2/blob/1.7.3/Controller/Response/Catalog.php#L97
 * @noinspection PhpParamsInspection
 * @param C|int $c
 */
function df_category_children($c) {return df_category_c()->addIsActiveFilter()->addIdFilter(df_category($c)->getChildren());}