<?php
use Df\Payment\Method as M;
use Magento\Quote\Model\Quote\Payment as QP;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Payment\Model\MethodInterface as IM;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Transaction as T;
/**
 * 2017-02-07
 * @param string|object $c
 * @param mixed|null $s [optional]
 * @return IM|M;
 */
function dfp_method($c, $s = null) {return dfcf(function($c, $s) {
	/** @var M $result */
	$result = df_o(dfp_method_c($c));
	$result->setStore($s);
	$result->setInfoInstance(df_quote()->getPayment());
	return $result;
}, [df_cts($c), df_store_id($s)]);}

/**
 * 2017-03-12
 * 2017-03-14
 * Отныне у результата уже инициализирован магазин: @see \Df\Payment\Method::setInfoInstance()
 * @used-by dfp_refund()
 * @used-by \Df\Payment\Observer\DataProvider\SearchResult::execute()
 * @used-by \Df\Payment\PlaceOrderInternal::s()
 * @used-by \Df\Payment\W\Handler::m()
 * @used-by \Dfe\CheckoutCom\Handler\CustomerReturn::action()
 * @param II|OP|QP $p
 * @return M
 */
function dfp_method_by_p(II $p) {return df_ar($p->getMethodInstance(), M::class);}

/**
 * 2016-08-20
 * 2017-03-14
 * Отныне у результата уже инициализирован магазин: @see \Df\Payment\Method::setInfoInstance()
 * @see df_trans_by_payment()
 * @param T $t
 * @return IM|M;
 */
function dfp_method_by_trans(T $t) {return dfp_by_trans($t)->getMethodInstance();}

/**
 * 2017-03-11  
 * @used-by dfp_method()
 * @used-by \Df\Payment\W\Reader::__construct()
 * @used-by \Df\Payment\W\F::__construct()
 * @param string|object $c
 * @return string
 */
function dfp_method_c($c) {return df_ar(df_con($c, 'Method'), M::class);}

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
 * @param IM $m
 * @return bool
 */
function dfp_method_is_my(IM $m) {return $m instanceof M;}

/**
 * 2016-12-22
 * @used-by dfp_report()
 * @used-by dfp_sentry_tags()
 * @used-by \Df\Payment\W\Action::notImplemented()
 * @used-by \Df\Payment\W\Exception::mTitle()
 * @used-by \Df\Payment\Settings::titleB()
 * @used-by \Df\Payment\W\Handler::log()
 * @used-by \Df\Payment\W\Handler::log()
 * @used-by \Df\Payment\W\Handler::log()
 * @uses \Df\Payment\Method::titleBackendS()
 * @param string|object $c
 * @return string
 */
function dfp_method_title($c) {return dfcf(function($c) {return
	dfp_method_call_s($c, 'titleBackendS')
;}, [df_cts($c)]);}