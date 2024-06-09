<?php
use Magento\Framework\App\Filesystem\DirectoryList as DL;

/**
 * 2019-08-23
 * @used-by df_google_init_service_account()
 */
function df_fs_etc(string $p = ''):string {return df_cc_path(df_fs_dl()->getPath(DL::CONFIG), df_trim_ds_left($p));}