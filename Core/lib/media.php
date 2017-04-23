<?php
use Magento\Framework\App\Filesystem\DirectoryList as DL;
use Magento\Framework\Filesystem\Directory\Read as DirectoryRead;
use Magento\Framework\Filesystem\Directory\ReadInterface as DirectoryReadInterface;
use Magento\Framework\Filesystem\Directory\Write as DirectoryWrite;
use Magento\Framework\Filesystem\Directory\WriteInterface as DirectoryWriteInterface;
use Magento\Framework\UrlInterface as U;
/**
 * 2015-11-30
 * @param string $path [optional]
 * @return string
 */
function df_media_path_absolute($path = '') {return df_path_absolute(DL::MEDIA, $path);}

/**
 * 2015-12-01
 * https://mage2.pro/t/153
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
 * @return DirectoryRead|DirectoryReadInterface
 */
function df_media_reader() {return df_fs_r(DL::MEDIA);}

/**
 * 2015-11-29
 * 2017-04-03 The possible directory types for filesystem operations: https://mage2.pro/t/3591
 * @return DirectoryWrite|DirectoryWriteInterface
 */
function df_media_writer() {return df_fs_w(DL::MEDIA);}