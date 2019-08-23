<?php
use Magento\Catalog\Helper\Image as ImageH;
use Magento\Catalog\Model\Product as P;
// 2018-07-16 This class is present in Magento 2.0.0:
// https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Catalog/Model/Product/Media/Config.php
use Magento\Catalog\Model\Product\Media\Config as MC;
use Magento\Framework\App\Filesystem\DirectoryList as DL;

/**
 * 2016-04-23
 * @used-by df_product_image_url()
 * @used-by \SayItWithAGift\Options\Frontend::_toHtml()
 * @return ImageH
 */
function df_catalog_image_h() {return df_o(ImageH::class);}

/**
 * 2019-08-21
 * @used-by \Dfe\Color\Observer\ProductSaveBefore::execute()
 * @see df_media_path_absolute()
 * @param string $relative
 * @return string
 */
function df_product_image_path_absolute($relative) {return df_path_absolute(
	DL::MEDIA, 'catalog/product/' . df_trim_ds_left($relative)
);}

/**
 * 2016-04-23
 * How to get an image URL for a product programmatically? https://mage2.pro/t/1313
 * How is @uses \Magento\Catalog\Helper\Image::getUrl() implemented and used? https://mage2.pro/t/1316
 * @used-by df_oqi_image()
 * @used-by df_product_image_url()
 * @used-by \SayItWithAGift\Options\Frontend::_toHtml()
 * @see df_media_url()
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

/**
 * 2018-07-16
 * @used-by \SayItWithAGift\Options\Frontend::_toHtml()
 * @return MC
 */
function df_product_mc() {return df_o(MC::class);}