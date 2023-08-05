<?php
use Throwable as Th; # 2023-08-02 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311

/**
 * 2017-11-19
 * @used-by df_abstract()
 * @used-by df_sentry_ext_f()
 */
function df_caller_c(int $o = 0):string {return df_first(df_explode_method(df_caller_m(++$o)));}

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

/**
 * 2016-08-10
 * The original (not used now) implementation: https://github.com/mage2pro/core/blob/6.7.3/Core/lib/caller.php#L109-L111
 * 2017-01-12
 * The df_caller_ff() implementation: https://github.com/mage2pro/core/blob/6.7.3/Core/lib/caller.php#L113-L123
 * 2020-07-08 The function's new implementation is from the previous df_caller_ff() function.
 * @used-by df_oqi_amount()
 * @used-by df_prop()
 * @used-by \Df\API\Facade::p()
 * @used-by \Df\API\Settings::probablyTestable()
 * @used-by \Df\API\Settings::testable()
 * @used-by \Df\API\Settings::testableB()
 * @used-by \Df\API\Settings::testableGeneric()
 * @used-by \Df\API\Settings::testableP()
 * @used-by \Df\API\Settings::testablePV()
 * @used-by \Df\Config\O::filter()
 * @used-by \Df\Config\O::v()
 * @used-by \Df\Config\O::v0()
 * @used-by \Df\Config\Settings::_a()
 * @used-by \Df\Config\Settings::_font()
 * @used-by \Df\Config\Settings::_matrix()
 * @used-by \Df\Config\Settings::b()
 * @used-by \Df\Config\Settings::bv()
 * @used-by \Df\Config\Settings::csv()
 * @used-by \Df\Config\Settings::i()
 * @used-by \Df\Config\Settings::json()
 * @used-by \Df\Config\Settings::nat()
 * @used-by \Df\Config\Settings::nat0()
 * @used-by \Df\Config\Settings::nwb()
 * @used-by \Df\Config\Settings::nwbn()
 * @used-by \Df\Config\Settings::p()
 * @used-by \Df\Config\Settings::v()
 * @used-by \Df\Config\Source\WaitPeriodType::calculate()
 * @used-by \Df\Payment\Method::action()
 * @used-by \Df\Payment\Method::convert()
 * @used-by \Df\Payment\TM::response()
 * @used-by \Df\PaypalClone\Signer::_sign()
 * @used-by \Df\StripeClone\Method::transInfo()
 * @used-by \Df\Typography\Font::size()
 * @used-by \Dfe\AlphaCommerceHub\API\Facade\BankCard::op()
 * @used-by \Dfe\AlphaCommerceHub\Method::transInfo()
 * @used-by \Dfe\Dynamics365\Test\TestCase::p()
 * @used-by \KingPalm\B2B\Schema::f()
 */
function df_caller_f(int $o = 0):string {return df_caller_entry(++$o)['function'];}

/**
 * 2016-08-10
 * The original (not used now) implementation: https://github.com/mage2pro/core/blob/6.7.3/Core/lib/caller.php#L125-L136
 * 2017-03-28
 * The df_caller_mm() implementation: https://github.com/mage2pro/core/blob/6.7.3/Core/lib/caller.php#L155-L169
 * 2020-07-08 The function's new implementation is from the previous df_caller_mm() function.
 * 2023-07-26
 * 1) «Array to string conversion in vendor/mage2pro/core/Core/lib/caller.php on line 114»
 * https://github.com/mage2pro/core/issues/257
 * 2) The pevious error handling never worked correctly:
 * https://github.com/mage2pro/core/tree/9.8.4/Core/lib/caller.php#L114
 * @used-by df_cache_get_simple()
 * @used-by df_caller_c()
 * @used-by df_caller_ml()
 * @used-by df_prop()
 * @used-by df_sentry_extra_f()
 */
function df_caller_m(int $o = 0):string {return df_cc_method(df_assert(df_caller_entry(++$o,
	# 2023-07-26
	# "«The required key «class» is absent» is `df_log()` is called from `*.phtml`":
	# https://github.com/mage2pro/core/issues/259
	'df_bt_entry_is_method' /** @uses df_bt_entry_is_method() */
)));}

/**
 * 2016-08-29
 * @used-by df_abstract()
 * @used-by df_should_not_be_here()
 */
function df_caller_mh():string {return df_tag('b', [], df_caller_ml(1));}

/**
 * 2016-08-31
 * @used-by df_abstract()
 * @used-by df_caller_mh()
 */
function df_caller_ml(int $o = 0):string {return df_caller_m(++$o) . '()';}

/**
 * 2023-07-25
 * 2023-07-26
 * The previous implementation:
 * 		return df_module_name(df_caller_c(++$o))
 * https://github.com/mage2pro/core/blob/9.9.5/Core/lib/caller.php#L147
 * @used-by df_log()
 * @used-by df_log_l()
 * @used-by df_sentry()
 * @used-by df_sentry_m()
 */
function df_caller_module(int $o = 0):string {
	$e = df_assert(df_caller_entry(++$o, function(array $e):bool {return
		# 2023-07-26
		# "«The required key «class» is absent» is `df_log()` is called from `*.phtml`":
		# https://github.com/mage2pro/core/issues/259
		# 2023-08-05
		# 1) "«Module 'Monolog_Logger' is not correctly registered» in `lib/internal/Magento/Framework/Module/Dir.php:62`":
		# https://github.com/mage2pro/core/issues/318
		# 2) `Monolog_Logger` is not a Magento module, so I added `df_module_enabled()`.
		df_bt_entry_is_method($e) && df_module_enabled(df_bt_entry_class($e))
		# 2023-07-26
		# "If `df_log()` is called from a `*.phtml`,
		# then the `*.phtml`'s module should be used as the log source instead of `Magento_Framework`":
		# https://github.com/mage2pro/core/issues/268
		|| df_bt_entry_is_phtml($e)
	;}));
	# 2023-08-05 «Module 'Monolog_Logger::addRecord' is not correctly registered»: https://github.com/mage2pro/core/issues/317
	return df_bt_entry_is_method($e) ? df_module_name(df_bt_entry_class($e)) : df_module_name_by_path(df_bt_entry_file($e));
}