<?php
/**
 * 2021-03-25
 * @used-by df_interceptor()
 * @used-by df_trim_interceptor()
 */
const DF_INTERCEPTOR = '\Interceptor';

/**
 * 2016-01-01 «Magento 2 duplicates the «\Interceptor» string constant in 9 places»: https://mage2.pro/t/377
 * 2016-10-20
 * Making $c optional leads to the error «get_class() called without object from outside a class»: https://3v4l.org/k6Hd5
 * @used-by dfpm_c()
 * @param string|object $c
 */
function df_interceptor($c):string {return df_cts($c) . DF_INTERCEPTOR;}

/**
 * 2021-03-26
 * @used-by df_cts()
 * @used-by \Df\Framework\Plugin\EntityManager\TypeResolver::afterResolve()
 */
function df_trim_interceptor(string $c):string {return df_trim_text_right($c, DF_INTERCEPTOR);}