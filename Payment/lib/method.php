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
 * @param string|object $c
 * @param string $method
 * @param mixed[] $params [optional]
 * @return mixed
 */
function dfp_method_call_s($c, $method, ...$params) {return df_con_s($c, 'Method', $method, $params);}

/**
 * 2016-08-25
 * @uses \Df\Payment\Method::codeS()
 * @param string|object $c
 * @return string
 */
function dfp_method_code($c) {return dfcf(function($c) {return
	dfp_method_call_s($c, 'codeS')
;}, [df_cts($c)]);}

/**
 * 2016-08-25
 * Без префикса «dfe_»
 * @uses \Df\Payment\Method::codeS()
 * @param string|object $c
 * @return string
 */
function dfp_method_code_short($c) {return df_trim_text_left(dfp_method_code($c), 'dfe_');}

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
 * @param string|object $c
 * @return string
 */
function dfp_method_title($c) {return dfcf(function($c) {return
	dfp_method_call_s($c, 'titleBackendS')
;}, [df_cts($c)]);}