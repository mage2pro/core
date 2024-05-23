<?php
use Magento\Catalog\Model\Category\DataProvider as DP;
/**
 * 2024-05-23
 * 1) "Implement `df_category_data_provider()`": https://github.com/mage2pro/core/issues/390
 * 2) It exists in Magento ≥ 2.1: https://github.com/magento/magento2/commit/73d75d9e
 * @used-by df_category_dp_meta()
 */
function df_category_dp():DP {return df_o(DP::class);}

/**
 * 2024-05-23 "Implement `df_category_dp_meta()`": https://github.com/mage2pro/core/issues/391
 */
function df_category_dp_meta(array $r, array $aa, string $fs = 'content'):array {return array_merge_recursive($r, [$fs => [
	'children' => df_map_k(
		function(string $k, array $v):array {return [$k => ['arguments' => ['data' => ['config' => $v]]]];}
		,dfa(df_category_dp()->getAttributesMeta(df_eav_category()), $aa)
	)
]]);}