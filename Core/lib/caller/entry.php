<?php
use Throwable as Th; # 2023-08-02 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311

/**
 * 2017-03-28 If the function is called from a closure, then it will go up through the stask until it leaves all closures.
 * 2023-07-26 "Add the `$skip` optional parameter to `df_caller_entry()`": https://github.com/mage2pro/core/issues/281
 * @used-by df_caller_f()
 * @used-by df_caller_m()
 * @used-by df_caller_module()
 * @used-by df_log_l()
 * @used-by df_sentry()
 * @used-by df_x_entry()
 * @used-by \Df\Framework\Log\Dispatcher::handle()
 * @param Th|int|null|array(array(string => string|int)) $p [optional]
 * @param callable|null $f [optional]
 * @return array(string => string|int)
 */
function df_caller_entry($p = 0, $f = null, array $skip = []):array {
	/**
	 * 2018-04-24
	 * I do not understand why did I use `2 + $offset` here before.
	 * Maybe the @uses array_slice() was included in the backtrace in previous PHP versions (e.g. PHP 5.6)?
	 * array_slice() is not included in the backtrace in PHP 7.1.14 and in PHP 7.0.27
	 * (I have checked it in the both XDebug enabled and disabled cases).
	 * 2019-01-14
	 * It seems that we need `2 + $offset` because the stack contains:
	 * 1) the current function: df_caller_entry
	 * 2) the function who calls df_caller_entry: df_caller_f, df_caller_m, or \Df\Framework\Log\Dispatcher::handle
	 * 3) the function who calls df_caller_f, df_caller_m, or \Df\Framework\Log\Dispatcher::handle: it should be the result.
	 * So the offset is 2.
	 * The previous code failed the @see \Df\API\Facade::p() method in the inkifi.com store.
	 * 2023-07-26
	 * 1) "`df_caller_entry()` detects the current entry incorrectly": https://github.com/mage2pro/core/issues/260
	 * 2) We do not need `2 + $offset` anymore because the current implementation of @uses df_bt() already uses `1 + $p`.
	 * So I changed `df_bt(df_bt_inc($p, 2))` to `df_bt(df_bt_inc($p))`
	 * 2023-07-27
	 * We really need `2 + $offset`.
	 * 1) The first entry in df_bt() is the caller of df_bt(). It is `df_caller_entry` in our case.
	 * 2) The second entry in df_bt() is the caller of `df_caller_entry`. It should be skipped too.
	 */
	$bt = df_bt(df_bt_inc($p, 2)); /** @var array(int => array(string => mixed)) $bt */
	$skip = array_merge($skip, ['dfc', 'dfcf', 'unknown']);
	while ($r = array_shift($bt)) {/** @var array(string => string|int)|null $r */
		$f2 = $r['function']; /** @var string $f2 */
		# 2017-03-28
		# Надо использовать именно df_contains(),
		# потому что PHP 7 возвращает просто строку «{closure}», а PHP 5.6 и HHVM — «A::{closure}»: https://3v4l.org/lHmqk
		# 2020-09-24 I added "unknown" to evaluate expressions in IntelliJ IDEA's with xDebug.
		if (
			!df_contains($f2, '{closure}')
			# 2023-07-26 "Add the `$skip` optional parameter to `df_caller_entry()`": https://github.com/mage2pro/core/issues/281
			&& !in_array($f2, $skip)
			&& (!$f || call_user_func($f, $r))
		) {
			break;
		}
	}
	return df_eta($r); /** 2021-10-05 @uses array_shift() returns `null` for an empty array */
}