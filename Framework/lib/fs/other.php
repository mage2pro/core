<?php
use Magento\Framework\Filesystem\Directory\Write as DirectoryWrite;
use Magento\Framework\Filesystem\Directory\WriteInterface as IDirectoryWrite;
use Magento\Framework\Filesystem\Io\File as File;

/**
 * 2017-04-03 Портировал из РСМ. Никем не используется.
 * 2022-11-03 @deprecated It is unused.
 */
function df_fs_delete(string $p):void {File::rmdirRecursive(df_param_sne($p, 0));}

/**
 * 2015-11-29
 * 2017-04-03 The possible directory types for filesystem operations: https://mage2.pro/t/3591
 * @used-by df_file_write()
 * @used-by df_media_writer()
 * @used-by df_sync()
 * @return DirectoryWrite|IDirectoryWrite
 */
function df_fs_w(string $type) {return df_fs()->getDirectoryWrite($type);}

