<?php
use Magento\Catalog\Model\Category as C;

/**
 * 2021-11-30 @deprecated It is unused.
 * @see df_product_id()
 * @param C|int $c
 */
function df_category_id($c):int {return df_int($c instanceof C ? $c->getId() : $c);}