<?php
/**
 * 2026-05-07
 * @used-by df_is_catalog_product_list_filtered()
 */
function df_is_catalog_layered_view():bool {return df_handle('catalog_category_view_type_layered');}

/**
 * 2019-03-27
 * @used-by CabinetsBay\Catalog\Observer\LayoutLoadBefore::execute() (https://github.com/cabinetsbay/catalog/issues/3)
 * @used-by Frugue\Core\Plugin\Swatches\Helper\Media::afterGetImageConfig()
 */
function df_is_catalog_product_list():bool {return df_handle('catalog_category_view');}

/**
 * 2026-05-07
 * 1) `df_catalog_layer()->getState()->getFilters()` is not yet initialized on `layout_load_before`.
 * Use a later event (e.g. `layout_generate_blocks_after`) instead.
 * 2.1) "Replace `INDEX` with `NOINDEX` in the `<meta name='robots' content='INDEX,FOLLOW'/>` tag
 * on layered navigation pages": https://github.com/national-glass-partitions/core/issues/1
 * 2.2) https://github.com/magento/magento2/blob/2.4.9-beta1/app/code/Magento/LayeredNavigation/README.md?plain=1#L21
 * @used-by NGP\Core\Observer\LayoutGenerateBlocksAfter::execute() (https://github.com/national-glass-partitions/core/issues/1)
 */
function df_is_catalog_product_list_filtered():bool {return
	df_is_catalog_layered_view() && df_catalog_layer()->getState()->getFilters()
;}

/**
 * 2019-03-27
 * @used-by CabinetsBay\Catalog\Observer\LayoutLoadBefore::execute() (https://github.com/cabinetsbay/catalog/issues/47)
 * @used-by Dfe\Frontend\Block\ProductView\Css::_toHtml()
 * @used-by Frugue\Core\Plugin\Swatches\Helper\Media::afterGetImageConfig()
 * @used-by TFC\Core\Plugin\Theme\Block\Html\Breadcrumbs::aroundAddCrumb()
 */
function df_is_catalog_product_view():bool {return df_handle('catalog_product_view');}

/**
 * 2026-05-07
 * 1) "Replace `INDEX` with `NOINDEX` in the `<meta name='robots' content='INDEX,FOLLOW'/>` tag
 * on `catalogsearch/result` pages": https://github.com/national-glass-partitions/core/issues/2
 * 2) https://github.com/magento/magento2/blob/2.4.9-beta1/app/code/Magento/LayeredNavigation/README.md?plain=1#L23
 * @used-by NGP\Core\Observer\LayoutGenerateBlocksAfter::execute() (https://github.com/national-glass-partitions/core/issues/2)
 */
function df_is_catalog_search_result():bool {return df_handle('catalogsearch_result_index');}