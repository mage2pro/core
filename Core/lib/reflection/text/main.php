<?php
use ReflectionClass as RC;

/**
 * 2017-01-11 http://stackoverflow.com/a/666701
 * @used-by \Df\Payment\W\F::i()
 */
function df_class_check_abstract(string $c):bool {df_param_sne($c, 0); return (new RC(df_ctr($c)))->isAbstract();}

/**
 * 2016-05-06
 * By analogy with https://github.com/magento/magento2/blob/135f967/lib/internal/Magento/Framework/ObjectManager/TMap.php#L97-L99
 * 2016-05-23
 * Намеренно не объединяем строки в единное выражение, чтобы собака @ не подавляла сбои первой строки.
 * Такие сбои могут произойти при синтаксических ошибках в проверяемом классе
 * (похоже, getInstanceType как-то загружает код класса).
 * @used-by dfpm_c()
 * @used-by \Df\Payment\Block\Info::checkoutSuccess()
 */
function df_class_exists(string $c):bool {$c = df_ctr($c); return @class_exists($c);}

/**
 * 2016-01-01
 * 2016-10-20
 * Making $c optional leads to the error «get_class() called without object from outside a class»: https://3v4l.org/k6Hd5
 * @used-by df_class_my()
 * @param string|object $c
 */
function df_class_f($c):string {return df_first(df_explode_class($c));}

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
 * 2015-12-29
 * 2016-10-20
 * Making $c optional leads to the error «get_class() called without object from outside a class»: https://3v4l.org/k6Hd5
 * @used-by df_class_llc()
 * @used-by \Df\API\Facade::path()
 * @used-by \Df\Payment\W\F::aspect()
 * @used-by \Df\Qa\Trace\Formatter::p()
 * @used-by \Dfe\AlphaCommerceHub\Test\CaseT\BankCard\CancelPayment::t01()
 * @used-by \Dfe\AlphaCommerceHub\Test\CaseT\BankCard\CapturePayment::t01()
 * @used-by \Dfe\AlphaCommerceHub\Test\CaseT\BankCard\RefundPayment::t01()
 * @used-by \Dfe\AlphaCommerceHub\Test\CaseT\PayPal\CapturePayment::t01()
 * @used-by \Dfe\AlphaCommerceHub\Test\CaseT\PayPal\PaymentStatus::t01()
 * @used-by \Dfe\AlphaCommerceHub\Test\CaseT\PayPal\RefundPayment::t01()
 * @param string|object $c
 */
function df_class_l($c):string {return df_last(df_explode_class($c));}

/**
 * 2018-01-30
 * 2021-10-27 @deprecared It is unused.
 * @param string|object $c
 */
function df_class_llc($c):string {return strtolower(df_class_l($c));}

/**
 * 2016-01-01
 * 2016-10-20
 * Making $c optional leads to the error «get_class() called without object from outside a class»: https://3v4l.org/k6Hd5
 * https://3v4l.org/k6Hd5
 * @used-by \Df\Config\Plugin\Model\Config\SourceFactory::aroundCreate()
 * @param string|object $c
 */
function df_class_my($c):bool {return in_array(df_class_f($c), ['Df', 'Dfe', 'Dfr']);}

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
 * 2016-02-09
 * 2016-10-20
 * Making $c optional leads to the error «get_class() called without object from outside a class»: https://3v4l.org/k6Hd5
 * https://3v4l.org/k6Hd5
 * @used-by \Df\API\Settings::titleB()
 * @param string|object $c
 */
function df_class_second($c):string {return df_explode_class($c)[1];}

/**
 * 2016-02-09
 * 2016-10-20
 * Making $c optional leads to the error «get_class() called without object from outside a class»: https://3v4l.org/k6Hd5
 * @used-by df_ci_get()
 * @used-by df_ci_save()
 * @used-by df_oi_get()
 * @used-by df_oi_save()
 * @param string|object $c
 */
function df_class_second_lc($c):string {return df_lcfirst(df_class_second($c));}

/**
 * 2016-11-25 «Df\Sso\Settings\Button» => «Settings\Button»
 * 2017-02-11 «Df\Sso\Settings\IButton» => «Settings\Button»  
 * @used-by dfs_con()
 * @param string|object $c
 */
function df_class_suffix($c):string {/** @var string $r */
	$r = implode(df_cld($c), array_slice(df_explode_class($c), 2));
	if (interface_exists($c)) {
		if ($a = df_explode_class($r)) {/** @var string[] $a */
			$len = count($a); /** @var int $len */
			$last = $a[$len - 1]; /** @var string $last */
			$a[$len - 1] = 'I' !== $last[0] ? $last : substr($last, 1);
			$r = df_cc_class($a);
		}
	}
	return $r;
}

/**
 * 2016-10-15
 * 2016-10-20
 * Making $c optional leads to the error «get_class() called without object from outside a class»: https://3v4l.org/k6Hd5
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