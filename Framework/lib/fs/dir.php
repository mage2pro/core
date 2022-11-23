<?php
use Magento\Framework\App\Filesystem\DirectoryList as DL;

/**
 * 2019-08-23
 * @used-by df_google_init_service_account()
 */
function df_fs_etc(string $p = ''):string {return df_cc_path(df_fs_dl()->getPath(DL::CONFIG), df_trim_ds_left($p));}

/**
 * 2021-03-20
 * @uses \Magento\Framework\Filesystem\Io\File::checkAndCreateFolder() method exists even in Magento 2.0.0.
 * @used-by df_mkdir_log()
 * @throws Exception
 */
function df_mkdir(string $f):void {df_file()->checkAndCreateFolder($f);}

/**
 * 2021-03-20
 * It solves issues like «var/log/Magedelight_Firstdata_SOAPError.log" cannot be opened with mode "a"»:
 * https://github.com/canadasatellite-ca/site/issues/22
 * @used-by \Magedelight\Firstdata\Model\Api\AbstractInterface::__construct() (canadasatellite.ca)
 * @throws Exception
 */
function df_mkdir_log():void {df_mkdir(df_fs_dl()->getPath(DL::LOG));}