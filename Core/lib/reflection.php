<?php
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
		df_error_html("The <b>df_caller_f()</b> function is wrongly called from the «<b>{$result}</b>» closure.");
	}
	return $result;
}

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
 * 2016-02-08
 * Применение @uses dfa_flatten() делает возможным вызовы типа:
 * df_cc_class_uc('Aa', ['Bb', 'Cb']) => Aa\Bb\Cb
 * @see df_cc_class_uc()
 * @param string[] ...$args
 * @return string
 */
function df_cc_class(...$args) {return implode('\\', dfa_flatten($args));}

/**
 * 2016-03-25
 * Применение @uses dfa_flatten() делает возможным вызовы типа:
 * df_cc_class_uc('aa', ['bb', 'cc']) => Aa\Bb\Cc
 * Мы используем это в модулях Stripe и Checkout.com.
 * @see df_cc_class()
 * @param string[] ...$args
 * @return string
 */
function df_cc_class_uc(...$args) {return df_cc_class(df_ucfirst(dfa_flatten($args)));}

/**
 * 2016-08-10
 * Если класс не указан, то вернёт название функции.
 * Поэтому в качестве $a1 можно передавать null.
 * @param string|object|null|array(object|string)|array(string = string) $a1
 * @param string|null $a2 [optional]
 * @return string
 */
function df_cc_method($a1, $a2 = null) {
	return df_ccc('::',
		$a2 ? [df_cts($a1), $a2]
			: (!isset($a1['function']) ? $a1
				: [dfa($a1, 'class'), $a1['function']]
			)
	);
}

/**
 * 2016-01-01
 * @param string|object $class
 * @return string
 */
function df_class_first($class) {return df_first(df_explode_class($class));}

/**
 * 2015-12-29
 * @param string|object $class
 * @return string
 */
function df_class_last($class) {return df_last(df_explode_class($class));}

/**
 * 2015-12-29
 * @param string|object $class
 * @return string
 */
function df_class_last_lc($class) {return df_lcfirst(df_class_last($class));}

/**
 * 2016-07-10
 * Df\Payment\R\Response => Df\Payment\R\Request
 * @param string|object $class
 * @param string $suffix
 * @return string
 */
function df_class_replace_last($class, $suffix) {
	return df_cc_class(df_cc_class(df_head(df_explode_class($class))), $suffix);
}

/**
 * 2016-02-09
 * @param string|object $class
 * @return string
 */
function df_class_second($class) {return df_explode_class($class)[1];}

/**
 * 2016-02-09
 * @param string|object $class
 * @return string
 */
function df_class_second_lc($class) {return df_lcfirst(df_class_second($class));}

/**
 * 2016-01-01
 * @param string|object $class
 * @return bool
 */
function df_class_my($class) {return in_array(df_class_first($class), ['Df', 'Dfe', 'Dfr']);}

/**
 * 2016-08-04
 * 2016-08-10
 * @uses defined() не реагирует на методы класса, в том числе на статические,
 * поэтому нам использовать эту функию безопасно: https://3v4l.org/9RBfr
 * @used-by \Df\Config\O::ct()
 * @used-by \Df\Payment\Method::codeS()
 * @param string|object $class
 * @param string $name
 * @param mixed|callable $default [optional]
 * @return mixed
 */
function df_const($class, $name, $default = null) {
	/** @var string $nameFull */
	$nameFull = df_cts($class) . '::' . $name;
	return defined($nameFull) ? constant($nameFull) : df_call_if($default);
}

/**
 * 2016-02-08
 * Проверяет наличие следующих классов в указанном порядке:
 * 1) <имя конечного модуля>\<окончание класса>
 * 2) $defaultResult
 * Возвращает первый из найденных классов.
 * @param object|string $caller
 * @param string $suffix
 * @param string|null $defaultResult [optional]
 * @param bool $throwOnError [optional]
 * @return string|null
 */
function df_con($caller, $suffix, $defaultResult = null, $throwOnError = true) {
	return \Df\Core\Convention::s()->getClass($caller, $suffix, $defaultResult, $throwOnError);
}

/**
 * 2016-08-29
 * @used-by dfp_method_call_s()
 * @param string|object $caller
 * @param string $suffix
 * @param string $method
 * @param mixed[] $params [optional]
 * @return mixed
 */
function df_con_s($caller, $suffix, $method, array $params = []) {return dfcf(
	function($caller, $suffix, $method, array $params = []) {return
		call_user_func_array([df_con($caller, $suffix), $method], $params)
	;}
, func_get_args());}

/**
 * 2016-07-10
 * @param object|string $caller
 * @param string $classSuffix
 * @param string|null $defaultResult [optional]
 * @param bool $throwOnError [optional]
 * @return string|null
 */
function df_con_same_folder($caller, $classSuffix, $defaultResult = null, $throwOnError = true) {
	return \Df\Core\Convention::s()->getClassInTheSameFolder(
		$caller, $classSuffix, $defaultResult, $throwOnError
	);
}

/**
 * 2015-08-14
 * Обратите внимание, что @uses get_class() не ставит «\» впереди имени класса:
 * http://3v4l.org/HPF9R
	namespace A;
	class B {}
	$b = new B;
	echo get_class($b);
 * => «A\B»
 *
 * 2015-09-01
 * Обратите внимание, что @uses ltrim() корректно работает с кириллицей:
 * https://3v4l.org/rrNL9
 * echo ltrim('\\Путь\\Путь\\Путь', '\\');  => Путь\Путь\Путь
 *
 * @used-by df_explode_class()
 * @used-by df_module_name()
 * @param string|object|null $class
 * @param string $delimiter [optional]
 * @return string
 */
function df_cts($class, $delimiter = '\\') {
	/** @var string $result */
	$result = is_object($class) ? get_class($class) : ltrim($class, '\\');
	// 2016-01-29
	$result = df_trim_text_right($result, '\Interceptor');
	return '\\' === $delimiter ?  $result : str_replace('\\', $delimiter, $result);
}

/**
 * 2016-01-29
 * @param string $class
 * @param string $delimiter
 * @return string
 */
function df_cts_lc($class, $delimiter) {return implode($delimiter, df_explode_class_lc($class));}

/**
 * 2016-04-11
 * Dfe_CheckoutCom => dfe_checkout_com
 * @param string $class
 * @param string $delimiter
 * @return string
 */
function df_cts_lc_camel($class, $delimiter) {
	return implode($delimiter, df_explode_class_lc_camel($class));
}

/**
 * @param string|object $class
 * @return string[]
 */
function df_explode_class($class) {return explode('\\', df_cts($class));}

/**
 * 2016-04-11
 * Dfe_CheckoutCom => [Dfe, Checkout, Com]
 * @param string|object $class
 * @return string[]
 */
function df_explode_class_camel($class) {
	return dfa_flatten(df_explode_camel(explode('\\', df_cts($class))));
}

/**
 * 2016-01-14
 * @param string|object $class
 * @return string[]
 */
function df_explode_class_lc($class) {return df_lcfirst(df_explode_class($class));}

/**
 * 2016-04-11
 * Dfe_CheckoutCom => [dfe, checkout, com]
 * @param string|object $class
 * @return string[]
 */
function df_explode_class_lc_camel($class) {return df_lcfirst(df_explode_class_camel($class));}

/**
 * 2016-01-01
 * «Magento 2 duplicates the «\Interceptor» string constant in 9 places»:
 * https://mage2.pro/t/377
 * @param string|object $class
 * @return string
 */
function df_interceptor_name($class) {return df_cts($class) . '\Interceptor';}

/**
 * «Dfe\AllPay\Response» => «Dfe_AllPay»
 * @param \Magento\Framework\DataObject|string $object
 * @param string $delimiter [optional]
 * @return string
 */
function df_module_name($object, $delimiter = '_') {return dfcf(
	function($class, $delimiter) {return
		implode($delimiter, array_slice(df_explode_class($class), 0, 2))
	;}
, [df_cts($object), $delimiter]);}

/**
 * 2016-08-28
 * «Dfe\AllPay\Response» => «AllPay»
 * @param \Magento\Framework\DataObject|string $object
 * @return string
 */
function df_module_name_short($object) {return dfcf(function($class) {return
	df_explode_class($class)[1]
;}, [df_cts($object)]);}

/**
 * 2016-02-16
 * «Dfe\CheckoutCom\Method» => «dfe_checkout_com»
 * @param \Magento\Framework\DataObject|string $object
 * @param string $delimiter [optional]
 * @return string
 */
function df_module_name_lc($object, $delimiter = '_') {
	return implode($delimiter, df_explode_class_lc_camel(df_module_name($object, '\\')));
}