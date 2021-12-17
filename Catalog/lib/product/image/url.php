<?php
use Magento\Catalog\Helper\Image as ImageH;
use Magento\Catalog\Model\Product as P;

/**
 * 2021-11-30
 * @see df_product_image_url()
 * @param P $p
 * @param int|null $limit [optionaL]
 */
function df_product_images_additional(P $p, $limit = null) {
	$m = basename($p->getImage()); /** @var string $main */
	return df_slice(
		array_filter(df_column($p->getMediaGalleryImages(), 'url'), function($u) use($m) {return basename($u) !== $m;})
		,0 ,$limit
	);
}

/**
 * 2016-04-23
 * How to get an image URL for a product programmatically? https://mage2.pro/t/1313
 * How is @uses \Magento\Catalog\Helper\Image::getUrl() implemented and used? https://mage2.pro/t/1316
 * @used-by df_oqi_image()
 * @used-by df_product_image_url() Recursion
 * @see df_media_path2url()
 * @see df_product_image_path()
 * @see df_product_images_additional()
 * @param P $p
 * @param string|null $type [optional]
 * @param array(string => string) $attrs [optional]
 * @return string
 */
function df_product_image_url(P $p, $type = null, $attrs = []) {/** @var string|null $r */
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
		# Give priority to the «image» attribute.
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
	/**
	 * 2021-12-17
	 * 1) I use @uses df_path_n() to correct such URLs in Windows:
	 * https://localhost.com:2197/pub/media/catalog/product\cache\1a71b601d00b50184472f9cf7e7475a3\/c/u/cuba-nest-c14_compressed.jpg
	 * 2) https://3v4l.org/8iP17
	 */
	return df_path_n($r);
}