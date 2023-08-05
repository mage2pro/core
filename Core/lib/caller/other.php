<?php
use Throwable as T; # 2023-08-02 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311

/**
 * 2017-11-19
 * @used-by df_abstract()
 * @used-by df_sentry_ext_f()
 */
function df_caller_c(int $o = 0):string {return df_first(df_explode_method(df_caller_m(++$o)));}

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
 * @param T|int $p
 */
function df_caller_module($p = 0):string {return !($e = df_caller_entry_m(df_bt_inc($p))) ? 'Df_Core' : (
	# 2023-08-05 «Module 'Monolog_Logger::addRecord' is not correctly registered»: https://github.com/mage2pro/core/issues/317
	df_bt_entry_is_method($e) ? df_module_name(df_bt_entry_class($e)) : df_module_name_by_path(df_bt_entry_file($e))
);}