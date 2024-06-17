<?php
use Magento\Catalog\Model\Product\Attribute\Repository as R;

/**
 * 2019-08-21
 * @used-by df_product_att()
 */
function df_product_atts_r():R {return df_o(R::class);}