<?php
use Magento\Framework\App\Filesystem\DirectoryList as DL;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadFactory as DirectoryReadFactory;
use Magento\Framework\Filesystem\Directory\Write as DirectoryWrite;
use Magento\Framework\Filesystem\Directory\WriteInterface as IDirectoryWrite;
use Magento\Framework\Filesystem\Io\File as File;
use Magento\Framework\Filesystem\Io\Sftp;

/**
 * 2019-02-24
 * @used-by df_mkdir()
 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload\Pureprint::writeLocal()
 * @used-by \KingPalm\Core\Plugin\Aitoc\OrdersExportImport\Model\Processor\Config\ExportConfigMapper::aroundToConfig()
 */
function df_file():File {return df_o(File::class);}

/**
 * 2015-11-29
 * @used-by df_sys_reader()
 * @used-by df_fs_w()
 */
function df_fs():Filesystem {return df_o(Filesystem::class);}

/**
 * 2019-08-23
 * @used-by df_fs_etc()
 * @used-by df_mkdir_log()
 */
function df_fs_dl():DL {return df_o(DL::class);}

/**
 * 2020-06-16
 * @used-by \Df\SampleData\Model\Dependency::getModuleComposerPackageParent()
 */
function df_fs_rf():DirectoryReadFactory {return df_o(DirectoryReadFactory::class);}

/**
 * 2015-11-29
 * 2017-04-03 The possible directory types for filesystem operations: https://mage2.pro/t/3591
 * @used-by df_file_write()
 * @used-by df_media_writer()
 * @used-by df_sync()
 * @return DirectoryWrite|IDirectoryWrite
 */
function df_fs_w(string $type) {return df_fs()->getDirectoryWrite($type);}


/**
 * 2019-02-24
 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload::_p()
 */
function df_sftp():Sftp {return df_o(Sftp::class);}