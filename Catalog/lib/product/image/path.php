<?php
use Magento\Catalog\Model\Product as P;
use Magento\Framework\App\Filesystem\DirectoryList as DL;

/**
 * 2019-09-20
 * @see df_product_image_url()
 * @used-by \Dfe\Color\Observer\ProductImportBunchSaveAfter::execute()
 * @param array(string => string) $attrs [optional]
 */
function df_product_image_path(P $p, string $type = '', array $attrs = []):string {return df_media_url2path(df_product_image_url(
	$p, $type, $attrs
));}

/**
 * 2019-08-21
 * @used-by \Dfe\Color\Observer\ProductSaveBefore::execute()
 * @used-by \MageWorx\OptionFeatures\Helper\Data::getThumbImageUrl(canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/46)
 * @used-by \TFC\Core\Plugin\Catalog\Block\Product\View\GalleryOptions::afterGetOptionsJson()
 * @see df_media_path_abs()
 */
function df_product_image_path2abs(string $rel):string {return df_cc_path(df_product_images_path(), df_trim_ds_left($rel));}

/**
 * 2020-10-26
 * @used-by \TFC\Image\Command\C1::image()
 * @used-by \TFC\Image\Command\C1::p()
 */
function df_product_image_path2rel(string $abs):string {return df_trim_text_left($abs, df_product_images_path() . '/');}

/**
 * 2020-10-26
 * 2020-11-22 It does not end with `/`.
 * @used-by df_product_image_path2abs()
 * @used-by df_product_image_path2rel()
 * @used-by df_product_images_path_rel()
 * @used-by \TFC\Image\Command\C1::image()
 * @used-by \TFC\Image\Command\C1::images()
 * @used-by \TFC\Image\Command\C3::p()
 */
function df_product_images_path():string {return df_sys_path_abs(DL::MEDIA, 'catalog/product');}

/**
 * 2020-11-22 «pub/media/catalog/product»
 * @used-by \TFC\Image\Command\C3::p()
 */
function df_product_images_path_rel():string {return dfcf(function() {return df_path_rel(df_product_images_path());});}

/**
 * 2019-08-23
 * @used-by \Dfe\Color\Observer\ProductSaveBefore::execute()
 * @see df_media_path_abs()
 */
function df_product_image_tmp_path2abs(string $rel):string {return df_sys_path_abs(
	DL::MEDIA, 'tmp/catalog/product/' . df_trim_ds_left($rel)
);}