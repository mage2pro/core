<?php
use Magento\Catalog\Model\Category\DataProvider as DP;
/**
 * 2024-05-23
 * 1) "Implement `df_category_data_provider()`": https://github.com/mage2pro/core/issues/390
 * 2) It exists in Magento ≥ 2.1: https://github.com/magento/magento2/commit/73d75d9e
 */
function df_category_dp():DP {return df_o(DP::class);}