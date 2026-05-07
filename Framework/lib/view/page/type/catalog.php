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