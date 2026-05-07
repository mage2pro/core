<?php
/**
 * 2019-03-27
 * @used-by CabinetsBay\Catalog\Observer\LayoutLoadBefore::execute() (https://github.com/cabinetsbay/catalog/issues/3)
 * @used-by Frugue\Core\Plugin\Swatches\Helper\Media::afterGetImageConfig()
 */
function df_is_catalog_product_list():bool {return df_handle('catalog_category_view');}

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
 * @used-by NGP\Core\Observer\LayoutLoadBefore::execute() (https://github.com/national-glass-partitions/core/issues/2)
 */
function df_is_catalog_search_result():bool {return df_handle('catalogsearch_result_index');}