<?php
use Magento\Catalog\Helper\Image as ImageH;
use Magento\Catalog\Model\Product as P;
# 2018-07-16 This class is present in Magento 2.0.0:
# https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Catalog/Model/Product/Media/Config.php
use Magento\Catalog\Model\Product\Media\Config as MC;
use Magento\Framework\App\Filesystem\DirectoryList as DL;

/**
 * 2016-04-23
 * @used-by df_product_image_url()
 * @used-by app/design/frontend/MageBig/martfury/layout01/MageBig_QuickView/templates/product/view/gallery.phtml (innomuebles.com, https://github.com/innomuebles/m2/issues/7)
 * @used-by app/design/frontend/MageBig/martfury/layout01/MageBig_WidgetPlus/templates/widget/layout/view/gallery.phtml (innomuebles.com, https://github.com/innomuebles/m2/issues/7)
 * @used-by app/design/frontend/MageBig/martfury/layout01/Magento_Catalog/templates/product/view/gallery.phtml:25 (innomuebles.com, https://github.com/innomuebles/m2/issues/7)
 * @used-by \Justuno\M2\Catalog\Images::p()
 * @used-by \SayItWithAGift\Options\Frontend::_toHtml() 
 * @return ImageH
 */
function df_catalog_image_h() {return df_o(ImageH::class);}

/**
 * 2019-09-20
 * @see df_product_image_url()
 * @used-by \Dfe\Color\Observer\ProductImportBunchSaveAfter::execute()
 * @param P $p
 * @param string|null $type [optional]
 * @param array(string => string) $attrs [optional]
 * @return string
 */
function df_product_image_path(P $p, $type = null, $attrs = []) {return df_media_url2path(df_product_image_url(
	$p, $type, $attrs
));}

/**
 * 2019-08-21
 * @used-by \Dfe\Color\Observer\ProductSaveBefore::execute()
 * @used-by \MageWorx\OptionFeatures\Helper\Data::getThumbImageUrl(canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/46)
 * @used-by \TFC\Core\Plugin\Catalog\Block\Product\View\GalleryOptions::afterGetOptionsJson()
 * @see df_media_path_absolute()
 * @param string $rel
 * @return string
 */
function df_product_image_path2abs($rel) {return df_cc_path(df_product_images_path(), df_trim_ds_left($rel));}

/**
 * 2020-10-26
 * @used-by \TFC\Image\Command\C1::image()
 * @used-by \TFC\Image\Command\C1::p()
 * @param string $abs
 * @return string
 */
function df_product_image_path2rel($abs) {return df_trim_text_left($abs, df_product_images_path() . '/');}

/**
 * 2020-10-26
 * 2020-11-22 It does not end with `/`.
 * @used-by df_product_image_path2abs()
 * @used-by df_product_image_path2rel()
 * @used-by df_product_images_path_rel()
 * @used-by \TFC\Image\Command\C1::image()
 * @used-by \TFC\Image\Command\C1::images()
 * @used-by \TFC\Image\Command\C3::p()
 * @return string
 */
function df_product_images_path() {return df_path_absolute(DL::MEDIA, 'catalog/product');}

/**
 * 2020-11-22 «pub/media/catalog/product»
 * @used-by \TFC\Image\Command\C3::p()
 * @return string
 */
function df_product_images_path_rel() {return dfcf(function() {return df_path_relative(df_product_images_path());});}

/**
 * 2019-08-23
 * @used-by \Dfe\Color\Observer\ProductSaveBefore::execute()
 * @see df_media_path_absolute()
 * @param string $rel
 * @return string
 */
function df_product_image_tmp_path2abs($rel) {return df_path_absolute(
	DL::MEDIA, 'tmp/catalog/product/' . df_trim_ds_left($rel)
);}

/**
 * 2016-04-23
 * How to get an image URL for a product programmatically? https://mage2.pro/t/1313
 * How is @uses \Magento\Catalog\Helper\Image::getUrl() implemented and used? https://mage2.pro/t/1316
 * @used-by df_oqi_image()
 * @used-by df_product_image_url() Recursion
 * @see df_media_path2url()
 * @see df_product_image_path()
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
	return $r;
}

/**
 * 2018-07-16
 * @used-by \SayItWithAGift\Options\Frontend::_toHtml()
 * @return MC
 */
function df_product_mc() {return df_o(MC::class);}