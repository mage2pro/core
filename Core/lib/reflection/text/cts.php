<?php
/**
 * 2015-08-14 @uses get_class() does not add the leading slash `\` before the class name: http://3v4l.org/HPF9R
 * 2015-09-01
 * @uses ltrim() correctly handles Cyrillic letters: https://3v4l.org/rrNL9
 * 		echo ltrim('\\Путь\\Путь\\Путь', '\\');  => Путь\Путь\Путь
 * 2016-10-20 $c is required here because it is used by @used-by get_class(): https://3v4l.org/k6Hd5
 * @used-by df_explode_class()
 * @used-by df_interceptor()
 * @used-by df_module_name()
 * @used-by dfsm_code()
 * @used-by \Df\Payment\Method::getInfoBlockType()
 * @param string|object $c
 */
function df_cts($c, string $del = '\\'):string {/** @var string $r */
	$r = df_trim_interceptor(is_object($c) ? get_class($c) : ltrim($c, '\\'));
	return '\\' === $del ? $r : str_replace('\\', $del, $r);
}

/**
 * 2016-01-29
 * 2022-10-31 @deprecated It is unused.
 */
function df_cts_lc(string $c, string $del):string {return implode($del, df_explode_class_lc($c));}

/**
 * 2016-04-11 Dfe_CheckoutCom => dfe_checkout_com
 * 2023-01-30
 * «Argument 1 passed to df_cts_lc_camel() must be of the type string, object given,
 * called in vendor/mage2pro/core/Qa/lib/log.php on line 121»: https://github.com/mage2pro/core/issues/204
 * 2023-07-23 @deprecated It is unused.
 * @see df_module_name_lc()
 * @param string|object $c
 */
function df_cts_lc_camel($c, string $del):string {return implode($del, df_explode_class_lc_camel($c));}