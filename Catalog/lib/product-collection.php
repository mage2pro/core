<?php
use Magento\Catalog\Model\ResourceModel\Product\Collection as C;
/**
 * 2019-09-18
 * @return C
 */
function df_product_c() {return df_new_om(C::class);}