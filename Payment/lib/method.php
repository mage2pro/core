<?php
use Df\Payment\Method;
use Magento\Payment\Model\MethodInterface as IMethod;
use Magento\Sales\Model\Order\Payment\Transaction as T;
/**
 * 2016-08-20
 * @see df_trans_by_payment()
 * @param T $t
 * @return IMethod|Method;
 */
function dfp_method_by_trans(T $t) {return dfp_by_trans($t)->getMethodInstance();}

/**
 * 2016-08-25
 * @param string|object $caller
 * @param string $method
 * @param mixed[] $params [optional]
 * @return mixed
 */
function dfp_method_call_s($caller, $method, ...$params) {return
	df_con_s($caller, 'Method', $method, $params)
;}

/**
 * 2016-08-25
 * @uses \Df\Payment\Method::codeS()
 * @param string|object $caller
 * @return string
 */
function dfp_method_code($caller) {return dfcf(function($class) {return
	dfp_method_call_s($class, 'codeS')
;}, [df_cts($caller)]);}

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

/**
 * 2016-12-22
 * @uses \Df\Payment\Method::titleBackendS()
 * @param string|object $caller
 * @return string
 */
function dfp_method_title($caller) {return dfcf(function($class) {return
	dfp_method_call_s($class, 'titleBackendS')
;}, [df_cts($caller)]);}