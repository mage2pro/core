<?php
use Magento\Catalog\Block\Product\AbstractProduct as B;
use Magento\Catalog\Block\Product\View\Gallery as G;

/**
 * 2020-06-06
 * @used-by vendor/blushme/checkout/view/frontend/templates/extra/item.phtml:38 (blushme.se)
 * @return B
 */
function df_product_b() {return df_o(B::class);}

/**
 * 2020-10-28
 * @used-by \TFC\Core\Plugin\Catalog\Block\Product\View\GalleryOptions::afterGetOptionsJson()
 * @return G
 */
function df_product_gallery_b() {return df_o(G::class);}