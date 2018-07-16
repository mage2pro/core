<?php
use Magento\Catalog\Helper\Image as ImageH;
use Magento\Catalog\Model\Product as P;

/**
 * 2016-04-23
 * @return ImageH
 */
function df_catalog_image_h() {return df_o(ImageH::class);}

/**
 * 2016-04-23
 * How to get an image URL for a product programmatically?
 * https://mage2.pro/t/1313
 * How is @uses \Magento\Catalog\Helper\Image::getUrl() implemented and used?
 * https://mage2.pro/t/1316
 * @used-by df_oqi_image()
 * @used-by df_product_image_url()
 * @used-by \SayItWithAGift\Options\Frontend::getDataJson()
 * @param P $p
 * @param string|null $type [optional]
 * @param array(string => string) $attrs [optional]
 * @return string
 */
function df_product_image_url(P $p, $type = null, $attrs = []) {
	/** @var string|null $r */
	if ($type) {
		$r = df_catalog_image_h()
			->init($p, $type, ['type' => $type] + $attrs + df_view_config()->getMediaAttributes(
				'Magento_Catalog', ImageH::MEDIA_TYPE_CONFIG_NODE, $type
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
		$types = array_keys($p->getMediaAttributes());
		// Give priority to the «image» attribute.
		$key = array_search('image', $types); /** @var int|null $key */
		if (false !== $key) {
			unset($types[$key]);
			array_unshift($types, 'image');
		}
		$r = '';
		foreach ($types as $type) {
			$r = df_product_image_url($p, $type, $attrs);
			if ($r) {
				break;
			}
		}
	}
	return $r;
}