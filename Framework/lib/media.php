<?php
use Magento\Framework\App\Filesystem\DirectoryList as DL;
use Magento\Framework\Filesystem\Directory\Read as R;
use Magento\Framework\Filesystem\Directory\ReadInterface as IR;
use Magento\Framework\Filesystem\Directory\Write as W;
use Magento\Framework\Filesystem\Directory\WriteInterface as IW;
use Magento\Framework\Image\AdapterFactory as FAdapter;
use Magento\Framework\Image\Adapter\AbstractAdapter;
use Magento\Framework\Image\Adapter\AdapterInterface as IAdapter;
use Magento\Framework\Image\Adapter\Gd2;
use Magento\Framework\Image\Adapter\ImageMagick;
use Magento\Framework\UrlInterface as U;

/**
 * 2018-11-24
 * @used-by df_img_resize()
 * @return IAdapter|AbstractAdapter|Gd2|ImageMagick
 */
function df_img_adapter() {return df_img_adapter_f()->create();}

/**
 * 2018-11-24
 * @used-by df_img_adapter()
 */
function df_img_adapter_f():FAdapter {return df_o(FAdapter::class);}

/**
 * 2020-12-13
 * @used-by \TFC\Core\Plugin\MediaStorage\App\Media::aroundLaunch()
 */
function df_img_is_jpeg(string $f):bool {return in_array(strtolower(df_file_ext($f)), ['jpg', 'jpeg']);}

/**
 * 2018-11-24 $f is an image's path relative to the `pub/media` folder.
 * 2020-12-13 It is not used by `mage2pro/*` modules. I do not know who uses it.
 * 2022-11-23 @deprecated It is unused.
 * @param int|null $w [optional]
 * @param int|null $h [optional]
 */
function df_img_resize(string $f, $w = null, $h = null):string {
	$h = df_etn($h); $w = df_etn($w);
	$srcDirR = dirname($f); /** @var string $srcDirR */
	$dstDirR = df_cc_path($srcDirR, 'cache', "{$w}x{$h}"); /** @var string $dstDirR */
	$basename = basename($f); /** @var string $basename */
	$dstPathR = df_cc_path($dstDirR, $basename); /** @var string $dstPathR */
	$mw = df_media_writer(); /** @var W $mw */
	if (!$mw->isFile($dstPathR)) {
		$srcPathA = $mw->getAbsolutePath($f); /** @var string $srcPathA */
		$dstPathA = $mw->getAbsolutePath($dstPathR); /** @var string $dstPathA */
		$a = df_img_adapter(); /** @var IAdapter|AbstractAdapter|Gd2|ImageMagick $a */
		$a->open($srcPathA);
		$a->constrainOnly(true);
		$a->keepTransparency(true);
		$a->keepAspectRatio(true);
		$a->resize($w, $h);
		$a->save($dstPathA);
	}
	return df_media_path2url($dstPathR);
}

/**
 * 2015-11-30                                      
 * @used-by df_media_url2path()
 * @used-by \Df\GoogleFont\Fonts\Fs::absolute()
 * @used-by \TFC\Core\Plugin\MediaStorage\App\Media::aroundLaunch()
 * @used-by vendor/mage2pro/color/view/frontend/templates/index.phtml
 * @see df_product_image_path2abs()
 */
function df_media_path_absolute(string $p = ''):string {return df_path_absolute(DL::MEDIA, $p);}

/**
 * 2015-11-30 Левый «/» мы убираем.
 * @used-by df_media_path2url()
 */
function df_media_path_relative(string $p):string {return df_path_relative($p, DL::MEDIA);}

/**
 * 2015-11-30
 * 2020-12-13 @deprecated It is unused.
 * @return R|IR
 */
function df_media_reader() {return df_fs_r(DL::MEDIA);}

/**
 * 2015-12-01 https://mage2.pro/t/153
 * @used-by df_img_resize()
 * @used-by \Df\GoogleFont\Fonts\Png::url()
 * @used-by \Dfe\Markdown\FormElement::config()
 * @used-by \TemplateMonster\FilmSlider\Block\Widget\FilmSlider::addUrl() (frugue.com)
 * @used-by app/design/frontend/TradeFurnitureCompany/default/Magento_Theme/templates/finance.phtml (tradefurniturecompany.co.uk)
 * @used-by vendor/mage2pro/color/view/frontend/templates/index.phtml
 * @see df_media_url2path()
 * @see df_product_image_url()
 */
function df_media_path2url(string $p = ''):string {return df_store()->getBaseUrl(U::URL_TYPE_MEDIA) . df_media_path_relative($p);}

/**
 * 2019-09-20        
 * @used-by df_product_image_path()
 * @see df_media_path2url()
 */
function df_media_url2path(string $u = ''):string {return df_media_path_absolute(df_trim_text_left(
	$u, df_store()->getBaseUrl(U::URL_TYPE_MEDIA)
));}

/**
 * 2015-11-29
 * 2017-04-03 The possible directory types for filesystem operations: https://mage2.pro/t/3591
 * @used-by df_img_resize()
 * @return W|IW
 */
function df_media_writer() {return df_fs_w(DL::MEDIA);}

/**
 * 2020-12-13
 * 1) @see df_request() does not work
 * in the @see \TFC\Core\Plugin\MediaStorage\App\Media::aroundLaunch() context
 * because the @see \Magento\Framework\App\Request\Http singleton is not yet initialized there.
 * 2) The `/pub` can be absent (it depends on the webserver settings).
 * @used-by \TFC\Core\Plugin\MediaStorage\App\Media::aroundLaunch()
 */
function df_strip_media_from_request_uri():string {return
	df_trim_text_left(df_trim_text_left(dfa($_SERVER, 'REQUEST_URI'), '/pub'), '/' . DL::MEDIA . '/')
;}