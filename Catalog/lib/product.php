<?php
use Magento\Catalog\Helper\Product as ProductH;
use Magento\Catalog\Model\Product as P;
/**
 * 2018-06-04
 * @used-by \Frugue\Configurable\Plugin\Swatches\Block\Product\Renderer\Configurable::aroundGetAllowProducts()
 * @return ProductH
 */
function df_product_h() {return df_o(ProductH::class);}

/**
 * 2018-06-04
 * @used-by \Frugue\Configurable\Plugin\ConfigurableProduct\Helper\Data::aroundGetOptions()
 * @param int $id
 * @return P
 */
function df_product_load($id) {$r = df_new_om(P::class); return $r->load($id);}