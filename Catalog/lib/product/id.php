<?php
use Magento\Catalog\Model\Product as P;

/**
 * 2019-11-18
 * @see df_category_id()
 * @used-by df_qty()
 * @used-by df_review_summary()
 * @param P|int $p
 */
function df_product_id($p):int {return df_int(df_is_p($p) ? $p->getId() : $p);}