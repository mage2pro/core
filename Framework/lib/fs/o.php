<?php
use Magento\Framework\App\Filesystem\DirectoryList as DL;
use Magento\Framework\Filesystem;
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
 * @used-by df_sys_path_r()
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
 * 2019-02-24
 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload::_p()
 */
function df_sftp():Sftp {return df_o(Sftp::class);}