<?php
/**
 * 2016-08-24
 * @used-by df_is_catalog_product_list()
 * @used-by df_is_catalog_product_view()
 * @used-by df_is_checkout_success()
 * @used-by df_is_home()
 * @used-by df_is_login()
 * @used-by df_is_reg()
 */
function df_handle(string $n):bool {return in_array($n, df_handles());}

/**
 * 2017-08-25
 * 2022-11-03 @deprecated It is unused.
 */
function df_handle_prefix(string $p):bool {return !!df_find(
	function(string $h) use($p):bool {return df_starts_with($h, $p);}, df_handles()
);}

/**
 * 2015-12-21    
 * @used-by df_handle()
 * @used-by df_handle_prefix()
 * @return string[]
 */
function df_handles():array {return ($u = df_layout_update(null)) ? $u->getHandles() : [];}