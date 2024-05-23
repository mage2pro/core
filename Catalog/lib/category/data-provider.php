<?php
use Magento\Catalog\Model\Category\DataProvider as DP;
/**
 * 2024-05-23 "Implement `df_category_data_provider()`": https://github.com/mage2pro/core/issues/390
 */
function df_category_data_provider():DP {return df_o(DP::class);}