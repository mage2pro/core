<?php
/**
 * 2022-11-24
 * The @see DS constant exists in Magento 1: https://github.com/OpenMage/magento-mirror/blob/1.9.4.5/app/Mage.php#L27
 * It is absent in Magento 2.
 */
if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

/**
 * 2017-12-13
 * @used-by \Df\Payment\Method::canUseForCountryP()
 */
function df_add_ds_right(string $p):string {return df_trim_ds_right($p) . '/';}

/**
 * 2016-10-14
 * @used-by df_url_bp()
 */
function df_trim_ds(string $p):string {return df_trim($p, '/\\');}

/**
 * 2015-11-30
 * @used-by df_fs_etc()
 * @used-by df_path_absolute()
 * @used-by df_path_relative()
 * @used-by df_product_image_path2abs()
 * @used-by df_replace_store_code_in_url()
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