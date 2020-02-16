<?php
/**
 * 2016-08-24
 * @used-by df_is_catalog_product_list()
 * @used-by df_is_catalog_product_view()
 * @used-by df_is_checkout_success()
 * @used-by df_is_home()
 * @used-by df_is_login()
 * @used-by df_is_reg()
 * @param string $name
 * @return bool
 */
function df_handle($name) {return in_array($name, df_handles());}

/**
 * 2017-08-25
 * @param string $p
 * @return bool
 */
function df_handle_prefix($p) {return !!df_find(function($h) use($p) {return df_starts_with($h, $p);}, df_handles());}

/**
 * 2015-12-21    
 * @used-by df_handle()
 * @used-by df_handle_prefix()
 * @return string[]
 */
function df_handles() {return ($u = df_layout_update(null)) ? $u->getHandles() : [];}