<?php
use Df\Payment\Method;
use Magento\Sales\Model\Order\Payment\Transaction as T;
use Magento\Payment\Model\MethodInterface as IMethod;
/**
 * 2016-08-20
 * @see df_trans_by_payment()
 * @param T $t
 * @return IMethod|Method;
 */
function dfp_method_by_trans(T $t) {return dfp_by_trans($t)->getMethodInstance();}

/**
 * 2016-08-25
 * @param string|object $class
 * @param string $method
 * @param mixed[] ...$params [optional]
 * @return mixed
 */
function dfp_method_call_s($class, $method, ...$params) {
	/** @var array(string => mixed) $cache */
	static $cache;
	// 2016-08-25
	// При наличии параметров не кэшируем результат.
	if ($params) {
		$result = call_user_func_array([dfp_method_class($class), $method], $params);
	}
	else {
		$class = df_cts($class);
		/** @var string $key */
		$key = df_ckey($class, $method);
		if (!isset($cache[$key])) {
			$cache[$key] = df_n_set(call_user_func([dfp_method_class($class), $method]));
		}
		$result = df_n_get($cache[$key]);
	}
	return $result;
}

/**
 * 2016-08-25
 * @param string|object $class
 * @return string
 */
function dfp_method_class($class) {
	/** @var array(string => string) $cache */
	static $cache;
	/** @var string $key */
	$key = df_cts($class);
	if (!isset($cache[$key])) {
		$cache[$key] = df_convention($class, 'Method');
		df_assert_is(Method::class, $cache[$key]);
	}
	return $cache[$key];
}

/**
 * 2016-08-25
 * @uses \Df\Payment\Method::codeS()
 * @param string|object $class
 * @return string
 */
function dfp_method_code($class) {
	/** @var array(string => string) $cache */
	static $cache;
	/** @var string $key */
	$key = df_cts($class);
	if (!isset($cache[$key])) {
		$cache[$key] = dfp_method_call_s($class, 'codeS');
	}
	return $cache[$key];
}

/**
 * 2016-08-25
 * Без префикса «dfe_»
 * @uses \Df\Payment\Method::codeS()
 * @param string|object $class
 * @return string
 */
function dfp_method_code_short($class) {return df_trim_text_left(dfp_method_code($class), 'dfe_');}

/**
 * 2016-08-19
 * @see df_trans_is_my()
 * @used-by dfp_is_my()
 * @param IMethod $method
 * @return bool
 */
function dfp_method_is_my(IMethod $method) {return $method instanceof Method;}