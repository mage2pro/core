<?php
if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

/**
 * 2017-12-13
 * @used-by \Df\Payment\Method::canUseForCountryP()
 * @param string $p
 */
function df_add_ds_right($p):string {return df_trim_ds_right($p) . '/';}

/**
 * 2016-10-14
 * @used-by df_url_bp()
 * @param string $p
 */
function df_trim_ds($p):string {return df_trim($p, '/\\');}

/**
 * 2015-11-30
 * @used-by df_fs_etc()
 * @used-by df_path_absolute()
 * @used-by df_path_relative()
 * @used-by df_product_image_path2abs()
 * @used-by df_replace_store_code_in_url()
 * @used-by \Dfe\Salesforce\Test\Basic::url()
 * @used-by \TFC\Core\Router::match() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/core/issues/40)
 * @param string $p
 */
function df_trim_ds_left($p):string {return df_trim_left($p, '/\\');}

/**
 * 2016-10-14
 * @used-by df_add_ds_right()
 * @used-by df_magento_version_remote()
 * @used-by \Df\Payment\Method::canUseForCountryP()
 * @used-by \Dfe\BlackbaudNetCommunity\Url::build()
 * @used-by \Dfe\BlackbaudNetCommunity\Url::check()
 */
function df_trim_ds_right(string $p):string {return df_trim_right($p, '/\\');}