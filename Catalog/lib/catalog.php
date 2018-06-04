<?php
use Magento\Bundle\Model\Product\Type as Bundle;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Downloadable\Model\Product\Type as Downloadable;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
/**
 * 2016-02-25
 * https://github.com/magento/magento2/blob/e0ed4bad/app/code/Magento/Catalog/etc/adminhtml/di.xml#L10-L10
 * @return LocatorInterface|\Magento\Catalog\Model\Locator\RegistryLocator
 */
function df_catalog_locator() {return df_o(LocatorInterface::class);}

/**
 * 2016-05-01
 * How to programmatically detect whether a product is configurable?
 * https://mage2.pro/t/1501
 * @param Product $product
 * @return bool
 */
function df_configurable(Product $product) {return Configurable::TYPE_CODE === $product->getTypeId();}

/**
 * 2017-04-20
 * @param string $type
 * @return bool
 */
function df_product_type_composite($type) {return in_array($type, [
	Bundle::TYPE_CODE, Configurable::TYPE_CODE, Grouped::TYPE_CODE
]);}

/**
 * 2015-11-14
 * @param Product $product
 * @return bool
 */
function df_virtual_or_downloadable(Product $product) {return in_array(
	$product->getTypeId(), [Type::TYPE_VIRTUAL, Downloadable::TYPE_DOWNLOADABLE]
);}