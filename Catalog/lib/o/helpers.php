<?php
use Magento\Catalog\Helper\Data as H;
use Magento\Catalog\Helper\Output as OutputH;

/**
 * 2021-12-21 @deprecated It is unused.
 */
function df_catalog_h():H {return df_o(H::class);}

/**
 * 2020-10-30
 * @used-by df_category_att_html()
 * @used-by df_product_att_html()
 * @used-by app/design/frontend/TradeFurnitureCompany/default/Magento_Catalog/templates/category/description.phtml
 */
function df_catalog_output():OutputH {return df_o(OutputH::class);}