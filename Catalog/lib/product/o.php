<?php
use Magento\Catalog\Helper\Product as ProductH;

/**
 * 2018-06-04
 * @used-by \Frugue\Configurable\Plugin\Swatches\Block\Product\Renderer\Configurable::aroundGetAllowProducts()
 */
function df_product_h():ProductH {return df_o(ProductH::class);}