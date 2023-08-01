<?php
use ReflectionClass as RC;

/**
 * 2017-01-10
 * @uses df_cts() отсекает окончание «\Interceptor»: без этого функция работала бы не так, как мы хотим
 * (возвращала бы путь к файлу из папки «var/generation», а не из папки модуля).
 * Пример результата: «C:/work/mage2.pro/store/vendor/mage2pro/allpay/Webhook/ATM.php».
 * Пока эта функция никем не используется.
 * 2022-10-31 @deprecated It is unused.
 * @param string|object $c
 */
function df_class_file($c):string {return df_path_n((new RC(df_cts(df_ctr($c))))->getFileName());}

/**
 * 2016-07-10 «Df\PaypalClone\W\Handler» => «Df\PaypalClone\Request».
 * 2022-11-26
 * We can not declare the argument as `string ...$newSuffix` because such a syntax will reject arrays: https://3v4l.org/jFdPm
 * @used-by df_con_sibling()
 * @param string|object $c
 * @param string|string[] $newSuffix
 */
function df_class_replace_last($c, ...$newSuffix):string {return implode(df_cld($c), array_merge(
	df_head(df_explode_class($c)), dfa_flatten($newSuffix)
));}

/**
 * 2016-10-15
 * 2016-10-20
 * Making $c optional leads to the error «get_class() called without object from outside a class»: https://3v4l.org/k6Hd5
 * @used-by df_class_replace_last()
 * @used-by df_class_suffix()
 * @used-by df_con()
 * @used-by df_con_child()
 * @param string|object $c
 */
function df_cld($c):string {return df_contains(df_cts($c) , '\\') ? '\\' : '_';}

/**
 * 2016-08-04
 * 2016-08-10
 * @uses defined() не реагирует на методы класса, в том числе на статические,
 * поэтому нам использовать эту функию безопасно: https://3v4l.org/9RBfr
 * @used-by \Df\Config\O::ct()
 * @used-by \Df\Payment\Method::codeS()
 * @param string|object $c
 * @param mixed|callable $def [optional]
 * @return mixed
 */
function df_const($c, string $name, $def = null) {return
	defined($full = df_cts($c) . "::$name") ? constant($full) : df_call_if($def)
;}