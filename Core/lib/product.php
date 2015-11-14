<?php
use Magento\Catalog\Model\Product;
/**
 * 2015-11-14
 * @param Product $product
 * @return bool
 */
function df_virtual_or_downloadable(Product $product) {
	return in_array($product->getTypeId(), ['virtual', 'downloadable']);
}


