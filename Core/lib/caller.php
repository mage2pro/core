<?php
/**
 * 2017-03-28
 * Эту функцию можно вызывать из Closure,
 * и тогда она просто будет подниматься по стеку выше, пока не выйдет из Closure.
 * @used-by df_caller_ff()     
 * @used-by df_caller_mm()
 * @param int $offset [optional]
 * @return array(string => string|int)
 */
function df_caller_entry($offset = 0) {
	/** @var array(int => array(string => mixed)) $bt */
	$bt = array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 2 + $offset);
	/** @var array(string => string|int) $result */
	while ($result = array_shift($bt)) {
		/** @var string $f */
		$f = $result['function'];
		// 2017-03-28
		// Надо использовать именно df_contains(),
		// потому что PHP 7 возвращает просто строку «{closure}»,
		// а PHP 5.6 и HHVM — «A::{closure}»: https://3v4l.org/lHmqk
		if (!df_contains($f, '{closure}') && 'dfc' !== $f) {
			break;
		}
	}
	return $result;
}

/**
 * 2016-08-10
 * @param int $offset [optional]
 * @return string
 */
function df_caller_f($offset = 0) {
	/** @var string $result */
	$result = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3 + $offset)[2 + $offset]['function'];
	/**
	 * 2016-09-06
	 * Порой бывают случаи, когда @see df_caller_f() ошибочно вызывается из @see \Closure.
	 * @see \Df\Payment\Settings::currency()
	 * Добавил защиту от таких случаев.
	 */
	if (df_contains($result, '{closure}')) {
		df_error_html(
			"The <b>df_caller_f()</b> function is wrongly called from the «<b>{$result}</b>» closure."
		);
	}
	return $result;
}

/**
 * 2017-01-12
 * Эту функцию, в отличие от @see df_caller_f(), можно вызывать из Closure,
 * и тогда она просто будет подниматься по стеку выше, пока не выйдет из Closure.
 * @used-by \Df\StripeClone\Method::transInfo()
 * @param int $offset [optional]
 * @return string
 */
function df_caller_ff($offset = 0) {return df_caller_entry(++$offset)['function'];}

/**
 * 2016-08-10
 * @param int $offset [optional]
 * @return string
 */
function df_caller_m($offset = 0) {
	/** @var array(string => string) $bt */
	$bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3 + $offset)[2 + $offset];
	/** @var string $method */
	return $bt['class'] . '::' . $bt['function'];
}

/**
 * 2016-08-29
 * @return string
 */
function df_caller_mh() {return df_tag('b', [], df_caller_ml(1));}

/**
 * 2016-08-31
 * @used-by df_caller_mh()
 * @param int $offset [optional]
 * @return string
 */
function df_caller_ml($offset = 0) {return '\\' . df_caller_m(1 + $offset) . '()';}

/**
 * 2017-03-28 Работает аналогично @see df_caller_ff()
 * @used-by df_cache_get_simple()
 * @param int $offset [optional]
 * @return string
 */
function df_caller_mm($offset = 0) {
	/** @var array(string => int) $bt */
	$bt = df_caller_entry(++$offset);
	return $bt['class'] . '::' . $bt['function'];
}