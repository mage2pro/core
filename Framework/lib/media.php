<?php
use Magento\Framework\App\Filesystem\DirectoryList as DL;
use Magento\Framework\Filesystem\Directory\Read as R;
use Magento\Framework\Filesystem\Directory\ReadInterface as IR;
use Magento\Framework\Filesystem\Directory\Write as W;
use Magento\Framework\Filesystem\Directory\WriteInterface as IW;
use Magento\Framework\Image\Adapter\AbstractAdapter;
use Magento\Framework\Image\Adapter\AdapterInterface as IAdapter;
use Magento\Framework\Image\AdapterFactory as FAdapter;
use Magento\Framework\UrlInterface as U;

/**
 * 2018-11-24
 * @used-by df_img_resize()
 * @return IAdapter|AbstractAdapter
 */
function df_img_adapter() {return df_img_adapter_f()->create();}

/**
 * 2018-11-24
 * @used-by df_img_adapter()
 * @return FAdapter
 */
function df_img_adapter_f() {return df_o(FAdapter::class);}

/**
 * 2018-11-24
 * @param string $f An image's path relative to the `pub/media` folder
 * @param int|null $w [optional]
 * @param int|null $h [optional]
 * @return string
 */
function df_img_resize($f, $w = null, $h = null) {
	$w = $w ?: null; $h = $h ?: null;
	$srcDirR = dirname($f); /** @var string $srcDirR */
	$dstDirR = df_cc_path($srcDirR, 'cache', "{$w}x{$h}"); /** @var string $dstDirR */
	$basename = basename($f); /** @var string $basename */
	$dstPathR = df_cc_path($dstDirR, $basename); /** @var string $dstPathR */
	$mw = df_media_writer(); /** @var W $mw */
	if (!$mw->isFile($dstPathR)) {
		$srcPathA = $mw->getAbsolutePath($f); /** @var string $srcPathA */
		$dstPathA = $mw->getAbsolutePath($dstPathR); /** @var string $dstPathA */
		$a = df_img_adapter(); /** @var IAdapter|AbstractAdapter $a */
		$a->open($srcPathA);
		$a->constrainOnly(true);
		$a->keepTransparency(true);
		$a->keepAspectRatio(true);
		$a->resize($w, $h);
		$a->save($dstPathA);
	}
	return df_media_url($dstPathR);
}

/**
 * 2015-11-30
 * @param string $path [optional]
 * @return string
 */
function df_media_path_absolute($path = '') {return df_path_absolute(DL::MEDIA, $path);}

/**
 * 2015-12-01 https://mage2.pro/t/153         
 * @used-by df_img_resize()
 * @used-by \Df\GoogleFont\Fonts\Png::url()
 * @used-by \Dfe\Markdown\FormElement::config()
 * @used-by \TemplateMonster\FilmSlider\Block\Widget\FilmSlider::addUrl()
 * @param string $path [optional]
 * @return string
 */
function df_media_url($path = '') {return 
	df_store()->getBaseUrl(U::URL_TYPE_MEDIA) . df_media_path_relative($path)
;}

/**
 * 2015-11-30
 * Левый «/» мы убираем.
 * @param string $path
 * @return string
 */
function df_media_path_relative($path) {return df_path_relative($path, DL::MEDIA);}

/**
 * 2015-12-08
 * @param string $mediaPath
 * @return string
 */
function df_media_read($mediaPath) {return df_file_read(DL::MEDIA, df_media_path_relative($mediaPath));}

/**
 * 2015-11-30
 * @return R|IR
 */
function df_media_reader() {return df_fs_r(DL::MEDIA);}

/**
 * 2015-11-29
 * 2017-04-03 The possible directory types for filesystem operations: https://mage2.pro/t/3591
 * @used-by df_img_resize()
 * @return W|IW
 */
function df_media_writer() {return df_fs_w(DL::MEDIA);}

/**
 * 2015-11-29
 * @uses dechex()
 * http://php.net/manual/function.dechex.php
 * http://stackoverflow.com/a/15202156
 * @param int[] $rgb
 * @param string $prefix [optional]
 * @return string
 */
function df_rgb2hex(array $rgb, $prefix = '') {return
	$prefix . df_pad0(6, implode(array_map('dechex', df_int($rgb))))
;}