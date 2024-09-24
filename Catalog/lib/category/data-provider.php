<?php
use Magento\Catalog\Model\Category\DataProvider as DP;
/**
 * 2024-05-23
 * 1) "Implement `df_category_dp_meta()`": https://github.com/mage2pro/core/issues/391
 * 2) @see \Magento\Catalog\Model\Category\DataProvider exists in Magento ≥ 2.1: https://github.com/magento/magento2/commit/73d75d9e
 * 3) We can not use @see \Magento\Catalog\Model\Category\DataProvider as a singleton:
 * 3.1) https://github.com/magento/magento2/blob/2.4.7/app/code/Magento/Catalog/view/adminhtml/ui_component/category_form.xml#L38-L43
 * 3.2) @see \Magento\Catalog\Model\Category\DataProvider::__construct()
 * 3.3) https://github.com/mage2pro/core/issues/390
 * @used-by CabinetsBay\Catalog\Plugin\Category\DataProvider::afterPrepareMeta() (https://github.com/cabinetsbay/site/issues/98)
 * @param array(string => mixed) $r
 * @param string[] $atts
 */
function df_category_dp_meta(DP $dp, array $r, array $atts, string $fs = 'content'):array {return array_merge_recursive($r, [
	$fs => ['children' => df_map_k(
		function(string $k, array $v):array {return [$k => ['arguments' => ['data' => ['config' => $v]]]];}
		,dfa($dp->getAttributesMeta(df_eav_category()), $atts)
	)]
]);}