<?php
use Df\Core\Exception as DFE;

/**
 * 2016-09-06
 * Порой бывают случаи, когда @see df_caller_f() ошибочно вызывается из @see \Closure.
 * Добавил защиту от таких случаев.
 * @used-by df_caller_f()
 * @used-by df_caller_m()
 * @param string $r
 * @return string
 * @throws DFE
 */
function df_assert_not_closure($r) {
	if (df_contains($r, '{closure}')) {
		df_error_html("A <b>df_caller_*()</b> function is wrongly called from the «<b>{$r}</b>» closure.");
	}
	return $r;
}

/**
 * 2017-11-19
 * @used-by df_abstract()
 * @used-by df_sentry_extra_f()
 * @param int $o [optional]
 * @return string
 */
function df_caller_c($o = 0) {
	$bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3 + $o)[2 + $o]; /** @var array(string => string) $bt */
	return $bt['class'];
}

/**
 * 2017-03-28
 * Эту функцию можно вызывать из Closure,
 * и тогда она просто будет подниматься по стеку выше, пока не выйдет из Closure.
 * @used-by df_caller_ff()     
 * @used-by df_caller_mm()
 * @param int $o [optional]
 * @return array(string => string|int)
 */
function df_caller_entry($o = 0) {
	/** @var array(int => array(string => mixed)) $bt */
	/**
	 * 2018-04-24
	 * I do not understand why did I use `2 + $offset` here before.
	 * Maybe the @uses array_slice() was included in the backtrace in previous PHP versions (e.g. PHP 5.6)?
	 * array_slice() is not included in the backtrace in PHP 7.1.14 and in PHP 7.0.27
	 * (I have checked it in the both XDebug enabled and disabled cases).
	 * 2019-01-14
	 * It seems that we need `2 + $offset` because the stack contains:
	 * 1) the current function: df_caller_entry
	 * 2) the function who calls df_caller_entry: df_caller_ff or df_caller_mm
	 * 3) the function who calls df_caller_ff or df_caller_mm: it should be the result.
	 * So the offset is 2.
	 * The previous code failed the @see \Df\API\Facade::p() method in the inkifi.com store.
	 */
	$bt = array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 2 + $o);
	while ($r = array_shift($bt) /** @var array(string => string|int) $r */) {
		$f = $r['function']; /** @var string $f */
		// 2017-03-28
		// Надо использовать именно df_contains(),
		// потому что PHP 7 возвращает просто строку «{closure}»,
		// а PHP 5.6 и HHVM — «A::{closure}»: https://3v4l.org/lHmqk
		if (!df_contains($f, '{closure}') && !in_array($f, ['dfc', 'dfcf'])) {
			break;
		}
	}
	return $r;
}

/**
 * 2016-08-10
 * @used-by df_oqi_amount()
 * @used-by df_prop()
 * @used-by \Df\API\Settings::probablyTestable()
 * @used-by \Df\API\Settings::testable()
 * @used-by \Df\API\Settings::testableB()
 * @used-by \Df\API\Settings::testableGeneric()
 * @used-by \Df\API\Settings::testableP()
 * @used-by \Df\API\Settings::testablePV()
 * @used-by \Df\Config\O::filter()
 * @used-by \Df\Config\O::v()
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
 * @used-by \Df\Typography\Font::_size()
 * @used-by \Dfe\AlphaCommerceHub\API\Facade\BankCard::op()
 * @used-by \Dfe\Dynamics365\Test\TestCase::p()
 * @used-by \KingPalm\B2B\Schema::f()
 * @param int $o [optional]
 * @return string
 */
function df_caller_f($o = 0) {return df_assert_not_closure(
	debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3 + $o)[2 + $o]['function']
);}

/**
 * 2017-01-12
 * Эту функцию, в отличие от @see df_caller_f(), можно вызывать из Closure,
 * и тогда она просто будет подниматься по стеку выше, пока не выйдет из Closure.
 * @used-by \Df\API\Facade::p()
 * @used-by \Df\StripeClone\Method::transInfo()
 * @used-by \Dfe\AlphaCommerceHub\Method::transInfo()
 * @param int $o [optional]
 * @return string
 */
function df_caller_ff($o = 0) {return df_caller_entry(++$o)['function'];}

/**
 * 2016-08-10
 * @used-by df_caller_ml()
 * @used-by df_prop()
 * @used-by df_sentry_extra_f()
 * @param int $o [optional]
 * @return string
 */
function df_caller_m($o = 0) {
	$bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3 + $o)[2 + $o]; /** @var array(string => string) $bt */
	return $bt['class'] . '::' . df_assert_not_closure($bt['function']);
}

/**
 * 2016-08-29
 * @used-by df_abstract()
 * @used-by df_should_not_be_here()
 * @return string
 */
function df_caller_mh() {return df_tag('b', [], df_caller_ml(1));}

/**
 * 2016-08-31
 * @used-by df_abstract()
 * @used-by df_caller_mh()
 * @param int $o [optional]
 * @return string
 */
function df_caller_ml($o = 0) {return df_caller_m(1 + $o) . '()';}

/**
 * 2017-03-28 Работает аналогично @see df_caller_ff()
 * @used-by df_cache_get_simple()
 * @param int $o [optional]
 * @return string
 */
function df_caller_mm($o = 0) {
	$bt = df_caller_entry(++$o); /** @var array(string => int) $bt */
	$class = dfa($bt, 'class'); /** @var string $class */
	if (!$class) {
		df_log_l(null, $m = "df_caller_mm(): no class.\nbt is:\n" . $bt); /** @var string $m */
		df_error($m);
	}
	return "$class::{$bt['function']}";
}