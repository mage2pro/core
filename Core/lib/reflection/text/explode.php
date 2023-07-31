<?php
/**
 * @used-by df_class_f()
 * @used-by df_class_l()
 * @used-by df_class_replace_last()
 * @used-by df_class_second()
 * @used-by df_class_suffix()
 * @used-by df_cts_lc()
 * @used-by df_explode_class_lc()
 * @param string|object $c
 * @return string[]
 */
function df_explode_class($c):array {return df_explode_multiple(['\\', '_'], df_cts($c));}

/**
 * 2016-04-11 Dfe_CheckoutCom => [Dfe, Checkout, Com]
 * 2016-10-20
 * Making $c optional leads to the error «get_class() called without object from outside a class»: https://3v4l.org/k6Hd5
 * @used-by df_explode_class_lc_camel()
 * @param string|object $c
 * @return string[]
 */
function df_explode_class_camel($c):array {return dfa_flatten(df_explode_camel(explode('\\', df_cts($c))));}

/**
 * 2016-01-14
 * 2016-10-20
 * Making $c optional leads to the error «get_class() called without object from outside a class»: https://3v4l.org/k6Hd5
 * @param string|object $c
 * @return string[]
 */
function df_explode_class_lc($c):array {return df_lcfirst(df_explode_class($c));}

/**
 * 2016-04-11
 * 2016-10-20
 * 1) Making $c optional leads to the error «get_class() called without object from outside a class»: https://3v4l.org/k6Hd5
 * 2) Dfe_CheckoutCom => [dfe, checkout, com]
 * @used-by df_module_name_lc()
 * @param string|object $c
 * @return string[]
 */
function df_explode_class_lc_camel($c):array {return df_lcfirst(df_explode_class_camel($c));}

/**
 * 2021-02-24
 * @used-by df_caller_c()
 * @return string[]
 */
function df_explode_method(string $m):array {return explode('::', $m);}
