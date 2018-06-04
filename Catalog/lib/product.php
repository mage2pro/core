<?php
use Magento\Catalog\Helper\Product as ProductH;
/**
 * 2018-06-04
 * @used-by \Frugue\Configurable\Plugin\ConfigurableProduct\Block\Product\View\Type\Configurable::aroundGetAllowProducts()
 * @return ProductH
 */
function df_product_h() {return df_o(ProductH::class);}