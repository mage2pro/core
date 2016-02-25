<?php
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Model\Product;
/**
 * 2016-02-25
 * https://github.com/magento/magento2/blob/e0ed4bad/app/code/Magento/Catalog/etc/adminhtml/di.xml#L10-L10
 * @return LocatorInterface|\Magento\Catalog\Model\Locator\RegistryLocator
 */
function df_catalog_locator() {return df_o(LocatorInterface::class);}
/**
 * 2015-11-14
 * @param Product $product
 * @return bool
 */
function df_virtual_or_downloadable(Product $product) {
	return in_array($product->getTypeId(), ['virtual', 'downloadable']);
}


