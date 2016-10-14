<?php
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Directory\Read as DirectoryRead;
use Magento\Framework\Filesystem\Directory\ReadInterface as DirectoryReadInterface;
use Magento\Framework\Filesystem\Directory\Write as DirectoryWrite;
use Magento\Framework\Filesystem\Directory\WriteInterface as DirectoryWriteInterface;
use Magento\Framework\UrlInterface;
/**
 * 2015-11-30
 * @param string $path [optional]
 * @return string
 */
function df_media_path_absolute($path = '') {return df_path_absolute(DirectoryList::MEDIA, $path);}

/**
 * 2015-12-01
 * https://mage2.pro/t/topic/153
 * @param string $path [optional]
 * @return string
 */
function df_media_url($path = '') {
	return df_store()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . df_media_path_relative($path);
}

/**
 * 2015-11-30
 * Левый «/» мы убираем.
 * @param string $path
 * @return string
 */
function df_media_path_relative($path) {return df_path_relative($path, DirectoryList::MEDIA);}

/**
 * 2015-12-08
 * @param string $mediaPath
 * @return string
 */
function df_media_read($mediaPath) {
	return df_file_read(DirectoryList::MEDIA, df_media_path_relative($mediaPath));
}

/**
 * 2015-11-30
 * @return DirectoryRead|DirectoryReadInterface
 */
function df_media_reader() {return df_fs_r(DirectoryList::MEDIA);}

/**
 * 2015-11-29
 * @return DirectoryWrite|DirectoryWriteInterface
 */
function df_media_writer() {return df_fs_w(DirectoryList::MEDIA);}

/**
 * 2015-11-30
 * Иерархия папок создаётся автоматически:
 * @see \Magento\Framework\Filesystem\Directory\Write::openFile()
 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Filesystem/Directory/Write.php#L247
 * @param string $mediaPath
 * @param string $contents
 * @return void
 */
function df_media_write($mediaPath, $contents) {
	df_file_write(DirectoryList::MEDIA, df_media_path_relative($mediaPath), $contents);
}


