<?php
/**
 * 2016-10-14
 * @used-by df_cc_path()
 * @used-by df_url_bp()
 * @param string|string[] ...$a
 * @return string|string[]
 */
function df_trim_ds(...$a) {return df_call_a(function(string $p):string {return df_trim($p, '/\\');}, $a);}

/**
 * 2015-11-30
 * @used-by df_fs_etc()
 * @used-by df_path_abs()
 * @used-by df_path_rel()
 * @used-by df_product_image_path2abs()
 * @used-by df_replace_store_code_in_url()
 * @used-by df_sys_path_abs()
 * @used-by \Dfe\Salesforce\Test\Basic::url()
 * @used-by \TFC\Core\Router::match() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/core/issues/40)
 */
function df_trim_ds_left(string $p):string {return df_trim_left($p, '/\\');}

/**
 * 2016-10-14
 * @used-by df_add_ds_right()
 * @used-by df_magento_version_remote()
 * @used-by \Df\Payment\Method::canUseForCountryP()
 * @used-by \Dfe\BlackbaudNetCommunity\Url::build()
 * @used-by \Dfe\BlackbaudNetCommunity\Url::check()
 */
function df_trim_ds_right(string $p):string {return df_trim_right($p, '/\\');}