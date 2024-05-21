<?php
use Magento\Catalog\Model\Product as P;

/**
 * 2024-05-22 "Implement `df_is_p()`": https://github.com/mage2pro/core/issues/383
 * @used-by df_product()
 * @param mixed $v
 */
function df_is_p($v):bool {return $v instanceof P;}