<?php
/**
 * 2024-03-04 "Implement `df_bt_filter_head()`": https://github.com/mage2pro/core/issues/356
 * 2024-06-03
 * 1) "Use the `callable` type": https://github.com/mage2pro/core/issues/404
 * 2) `callable` can be nullable: https://github.com/mage2pro/core/issues/174#user-content-callable	 
 * @used-by df_caller_entry()
 * @used-by \Df\Qa\Trace::__construct()
 * @param array(array(string => string|int)) $r
 */
function df_bt_filter_head(array $bt, array $skip = [], ?callable $f = null):array {
	$skip = array_merge($skip, ['dfc', 'dfcf' ,'unknown']);
	return df_filter_head($bt, function(array $e) use ($f, $skip) {/** @var array(string => string|int)|null $e */
		$f2 = df_bt_entry_func($e); /** @var string $f2 */
		# 2017-03-28
		# Надо использовать именно df_contains(),
		# потому что PHP 7 возвращает просто строку «{closure}», а PHP 5.6 и HHVM — «A::{closure}»: https://3v4l.org/lHmqk
		# 2020-09-24 I added "unknown" to evaluate expressions in IntelliJ IDEA's with xDebug.
		return (
			df_contains($f2, '{closure}')
			# 2023-07-26 "Add the `$skip` optional parameter to `df_caller_entry()`": https://github.com/mage2pro/core/issues/281
			|| in_array($f2, $skip)
			|| $f && !call_user_func($f, $e)
		);
	});
}