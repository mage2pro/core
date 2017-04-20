<?php
use Magento\Bundle\Model\Product\Type as Bundle;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Helper\Image as ImageHelper;
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
 * 2016-04-23
 * @return ImageHelper
 */
function df_catalog_image_h() {return df_o(ImageHelper::class);}

/**
 * 2017-04-20
 * @param string $type
 * @return bool
 */
function df_product_type_composite($type) {return in_array($type, [
	Bundle::TYPE_CODE, Configurable::TYPE_CODE, Grouped::TYPE_CODE
]);}

/**
 * 2016-05-01
 * How to programmatically detect whether a product is configurable?
 * https://mage2.pro/t/1501
 * @param Product $product
 * @return bool
 */
function df_configurable(Product $product) {return Configurable::TYPE_CODE === $product->getTypeId();}

/**
 * 2016-04-23
 * How to get an image URL for a product programmatically?
 * https://mage2.pro/t/1313
 * How is @uses \Magento\Catalog\Helper\Image::getUrl() implemented and used?
 * https://mage2.pro/t/1316
 * @used-by df_oqi_image()
 * @param Product $product
 * @param string|null $type [optional]
 * @param array(string => string) $attrs [optional]
 * @return string
 */
function df_product_image_url(Product $product, $type = null, $attrs = []) {
	/** @var string|null $result */
	if ($type) {
		$result = df_catalog_image_h()
			->init($product, $type, ['type' => $type] + $attrs + df_view_config()->getMediaAttributes(
				'Magento_Catalog', ImageHelper::MEDIA_TYPE_CONFIG_NODE, $type
			))
			->getUrl()
		;
	}
	else {
		/**
		 * 2016-05-02
		 * How is @uses \Magento\Catalog\Model\Product::getMediaAttributes() implemented and used?
		 * https://mage2.pro/t/1505
		 * @var string[] $types
		 */
		$types = array_keys($product->getMediaAttributes());
		// Give priority to the «image» attribute.
		/** @var int|null $key */
		$key = array_search('image', $types);
		if (false !== $key) {
			unset($types[$key]);
			array_unshift($types, 'image');
		}
		$result = '';
		foreach ($types as $type) {
			$result = df_product_image_url($product, $type, $attrs);
			if ($result) {
				break;
			}
		}
	}
	return $result;
}
/**
 * 2015-11-14
 * @param Product $product
 * @return bool
 */
function df_virtual_or_downloadable(Product $product) {
	return in_array($product->getTypeId(), [Type::TYPE_VIRTUAL, Downloadable::TYPE_DOWNLOADABLE]);
}


