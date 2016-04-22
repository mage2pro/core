<?php
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Model\Product;
/**
 * 2016-02-25
 * https://github.com/magento/magento2/blob/e0ed4bad/app/code/Magento/Catalog/etc/adminhtml/di.xml#L10-L10
 * @return LocatorInterface|\Magento\Catalog\Model\Locator\RegistryLocator
 */
function df_catalog_locator() {return df_o(LocatorInterface::class);}
/**
 * 2016-04-23
 * @return ImageHelper
 */
function df_catalog_image_h() {return df_o(ImageHelper::class);}
/**
 * 2016-04-23
 * How is @uses \Magento\Catalog\Helper\Image::getUrl() implemented and used?
 * https://mage2.pro/t/1316
 * How to get the base image URL form a product programmatically?
 * https://mage2.pro/t/1313
 * @param Product $product
 * @param string $type [optional]
 * @return string
 */
function df_product_image_url(Product $product, $type = 'product_page_image_large') {
	return df_catalog_image_h()->init($product, $type)->getUrl();
}
/**
 * 2015-11-14
 * @param Product $product
 * @return bool
 */
function df_virtual_or_downloadable(Product $product) {
	return in_array($product->getTypeId(), ['virtual', 'downloadable']);
}


