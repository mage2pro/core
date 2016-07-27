<?php
/**
 * 2016-02-08
 * @param ...$args
 * @return string
 */
function df_cc_class(...$args) {return implode('\\', df_args($args));}

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
 * 2016-02-08
 * Проверяет наличие следующих классов в указанном порядке:
 * 1) <имя конечного модуля>\<окончание класса>
 * 2) $defaultResult
 * Возвращает первый из найденных классов.
 * @param object|string $caller
 * @param string $classSuffix
 * @param string|null $defaultResult [optional]
 * @param bool $throwOnError [optional]
 * @return string|null
 */
function df_convention($caller, $classSuffix, $defaultResult = null, $throwOnError = true) {
	return \Df\Core\Convention::s()->getClass($caller, $classSuffix, $defaultResult, $throwOnError);
}

/**
 * 2016-07-10
 * @param object|string $caller
 * @param string $classSuffix
 * @param string|null $defaultResult [optional]
 * @param bool $throwOnError [optional]
 * @return string|null
 */
function df_convention_same_folder($caller, $classSuffix, $defaultResult = null, $throwOnError = true) {
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
 * @param string|object $class
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
 * Dfe_CheckoutCom => dfe_checkout_com]
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
 * 2016-03-25
 * @param ...$args
 * @return string
 */
function df_implode_class(...$args) {return implode('\\', df_ucfirst(dfa_flatten($args)));}

/**
 * 2016-01-01
 * «Magento 2 duplicates the «\Interceptor» string constant in 9 places»:
 * https://mage2.pro/t/377
 * @param string|object $class
 * @return string
 */
function df_interceptor_name($class) {return df_cts($class) . '\Interceptor';}

/**
 * «Df_SalesRule_Model_Event_Validator_Process» => «Df_SalesRule»
 * @param \Magento\Framework\DataObject|string $object
 * @param string $delimiter [optional]
 * @return string
 */
function df_module_name($object, $delimiter = '_') {
	return \Df\Core\Reflection::s()->getModuleName(df_cts($object), $delimiter);
}

/**
 * 2016-02-16
 * «Df_SalesRule_Model_Event_Validator_Process» => «df_salesRule»
 * @param \Magento\Framework\DataObject|string $object
 * @param string $delimiter [optional]
 * @return string
 */
function df_module_name_lc($object, $delimiter = '_') {
	return implode($delimiter, df_lcfirst(explode($delimiter, df_module_name($object, $delimiter))));
}