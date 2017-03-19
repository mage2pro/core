<?php
use Df\Payment\Method as M;
use Magento\Quote\Model\Quote\Payment as QP;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Payment\Model\MethodInterface as IM;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Transaction as T;
/**
 * 2017-02-07
 * 2017-03-17
 * 2017-03-19 https://3v4l.org/cKd6A
 * @used-by dfp_refund()
 * @used-by dfpm_title()
 * @used-by \Df\Payment\ConfigProvider::m()
 * @used-by \Df\Payment\Observer\DataProvider\SearchResult::execute()
 * @used-by \Df\Payment\Observer\FormatTransactionId::execute()
 * @used-by \Df\Payment\PlaceOrderInternal::s()
 * @used-by \Df\Payment\TestCase::m()
 * @used-by \Df\Payment\W\F::s()
 * @used-by \Df\Payment\W\Handler::m()
 * @used-by \Dfe\CheckoutCom\Handler\CustomerReturn::action()
 * При вызове с параметром в виде произвольного объекта, имени класса или модуля
 * функция будет использовать ТЕКУЩУЮ КОРЗИНУ в качестве II.
 * Будьте осторожны с этим в тех сценариях, когда текущей корзины нет.
 * @param mixed[] ...$args
 * @return M|IM
 */
function dfpm(...$args) {static $cache = []; return dfcf(function(...$args) use(&$cache) {
	/** @var array(string => M|IM) $cache */
	/** @var IM|II|T|object|string|null $src */
	if ($args) {
		$src = array_shift($args);
	}
	else {
		$src = df_quote()->getPayment();
		if (!$src->getMethod()) {
			df_error(
				'You can not use the dfpm() function without arguments here '
				. 'because the current customer has not chosen a payment method '
				. 'for the current quote yet.'
			);
		}
	}
	/** @var IM|M $result */
	if ($src instanceof IM) {
		$result = $src;
	}
	else {
		if ($src instanceof T) {
			$src = dfp_by_t($src);
		}
		if ($src instanceof II) {
			$result = $src->getMethodInstance();
		}
		else {
			/** @var string $c */
			if (!($result = dfa($cache, $c = dfpm_c($src)))) {
				$result = df_o($c);
				$result->setInfoInstance(df_quote()->getPayment());
			}
			if ($args) {
				$result->setStore(df_store_id($args[0]));
			}
		}
	}
	return $cache[dfpm_c($result)] = $result;
}, func_get_args());}

/**
 * 2017-03-11  
 * @used-by dfpm()
 * @used-by \Df\Payment\W\Reader::__construct()
 * @used-by \Df\Payment\W\F::__construct()
 * @param string|object $c
 * @return string
 */
function dfpm_c($c) {return df_ar(df_con($c, 'Method'), M::class);}

/**
 * 2016-08-25
 * @param string|object $c
 * @param string $method
 * @param mixed[] $params [optional]
 * @return mixed
 */
function dfpm_call_s($c, $method, ...$params) {return df_con_s($c, 'Method', $method, $params);}

/**
 * 2016-08-25
 * @uses \Df\Payment\Method::codeS()
 * @param string|object $c
 * @return string
 */
function dfpm_code($c) {return dfcf(function($c) {return dfpm_call_s($c, 'codeS');}, [df_cts($c)]);}

/**
 * 2016-08-25
 * Без префикса «dfe_»
 * @uses \Df\Payment\Method::codeS()
 * @param string|object $c
 * @return string
 */
function dfpm_code_short($c) {return df_trim_text_left(dfpm_code($c), 'dfe_');}

/**
 * 2016-08-19
 * @see df_trans_is_my()
 * @used-by dfp_is_my()
 * @used-by \Df\Payment\Observer\FormatTransactionId::execute()
 * @param IM $m
 * @return bool
 */
function dfpm_is_my(IM $m) {return $m instanceof M;}

/**
 * 2016-12-22
 * @used-by dfp_report()
 * @used-by dfp_sentry_tags()
 * @used-by \Df\Payment\W\Action::notImplemented()
 * @used-by \Df\Payment\W\Exception::mTitle()
 * @used-by \Df\Payment\Settings::titleB()
 * @used-by \Df\Payment\W\Handler::log()
 * @param string|object $c
 * @return string
 */
function dfpm_title($c) {return dfpm($c)->titleB();};