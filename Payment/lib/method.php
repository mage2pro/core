<?php
use Df\Core\R\ConT;
use Df\Payment\Method as M;
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\Quote\Payment as QP;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Payment\Model\MethodInterface as IM;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Transaction as T;

/**
 * 2016-08-19
 * @see df_trans_is_my()
 * @used-by dfp_sentry_tags()
 * @used-by dfpm_title()
 * @used-by dfps()
 * @used-by \Df\Payment\Observer\FormatTransactionId::execute()
 * @param IM|II|OP|QP|T|object|string|O|null $v
 */
function dfp_my($v):bool {return $v && dfpm($v) instanceof M;}

/**
 * 2017-02-07
 * 2017-03-17
 * 2017-03-19 https://3v4l.org/cKd6A
 * @used-by dfp_refund()
 * @used-by dfp_sentry_tags()
 * @used-by dfpex_args()
 * @used-by dfpm_title()
 * @used-by dfpmq()
 * @used-by dfps()
 * @used-by \Df\Payment\Choice::f()
 * @used-by \Df\Payment\Currency::f()
 * @used-by \Df\Payment\Observer\DataProvider\SearchResult::execute()
 * @used-by \Df\Payment\Observer\FormatTransactionId::execute()
 * @used-by \Df\Payment\PlaceOrderInternal::s()
 * @used-by \Df\Payment\TestCase::m()
 * @used-by \Df\Payment\Url::f()
 * @used-by \Df\Payment\W\Action::execute()
 * @used-by \Df\Payment\W\F::s()
 * @used-by \Df\Payment\W\Handler::m()
 * @used-by \Dfe\AlphaCommerceHub\API\Client::urlBase()
 * @used-by \Dfe\AlphaCommerceHub\W\Reader::reqFilter()
 * @used-by \Dfe\CheckoutCom\Handler\CustomerReturn::action()
 * @used-by \Dfe\Stripe\P\_3DS::p()
 * @used-by \Dfe\Stripe\P\Address::p()
 * @used-by \Dfe\TBCBank\Charge::p()
 * @used-by \Dfe\Vantiv\Charge::p()
 * При вызове с параметром в виде произвольного объекта, имени класса или модуля
 * функция будет использовать ТЕКУЩУЮ КОРЗИНУ в качестве II.
 * Будьте осторожны с этим в тех сценариях, когда текущей корзины нет.
 * @param mixed ...$args
 * @return M|IM
 */
function dfpm(...$args) {return dfcf(function(...$args) {
	/** @var IM|II|OP|QP|O|Q|T|object|string|null $src */
	if ($args) {
		$src = array_shift($args);
	}
	else {
		$src = dfp(df_quote());
		if (!$src->getMethod()) {
			df_error(
				'You can not use the dfpm() function without arguments here '
				.'because the current customer has not chosen a payment method for the current quote yet.'
			);
		}
	}
	/** @var IM|M $r */
	if ($src instanceof IM) {
		$r = $src;
	}
	else {
		if (df_is_oq($src) || $src instanceof T) {
			$src = dfp($src);
		}
		if ($src instanceof II) {
			$r = $src->getMethodInstance();
		}
		else {
			$r = M::sg($src);
			if ($args) {
				$r->setStore(df_store_id($args[0]));
			}
		}
	}
	return $r;
}, func_get_args());}

/**
 * 2017-03-11
 * При текущей реализации мы осознанно не поддерживаем interceptors, потому что:
 * 1) Похоже, что невозможно определить, имеется ли для некоторого класса interceptor,
 * потому что вызов @uses class_exists(interceptor) приводит к созданию interceptor'а
 * (как минимум — в developer mode), даже если его раньше не было.
 * 2) У нас потомки Method объявлены как final.
 * @used-by dfpm()
 * @used-by \Df\Payment\Block\Info::checkoutSuccess()
 * @used-by \Df\Payment\Method::sg()
 * @param string|object $c
 * @param bool $allowAbstract [optional]
 */
function dfpm_c($c, $allowAbstract = false):string {return dfcf(function($c, $allowAbstract = false) {return
	ConT::p($allowAbstract, function() use($c) {return df_con_heir($c, M::class);})
;}, func_get_args());}

/**
 * 2016-08-25
 * @param string|object $c
 * @param string $method
 * @param mixed ...$params [optional]
 * @return mixed
 */
function dfpm_call_s($c, $method, ...$params) {return df_con_s($c, 'Method', $method, $params);}

/**
 * 2016-08-25
 * @used-by dfpm_code_short()
 * @uses \Df\Payment\Method::codeS()
 * @param string|object $c
 */
function dfpm_code($c):string {return dfcf(function($c) {return dfpm_call_s($c, 'codeS');}, [df_cts($c)]);}

/**
 * 2016-08-25 Without the «dfe_» prefix.
 * @uses \Df\Payment\Method::codeS()
 * @used-by \Df\Payment\Settings::prefix()
 * @param string|object $c
 */
function dfpm_code_short($c):string {return df_trim_text_left(dfpm_code($c), 'dfe_');}

/**
 * 2016-12-22
 * @used-by dfp_report()
 * @used-by dfp_sentry_tags()
 * @used-by \Df\Payment\Comment\Description::locations()
 * @used-by \Df\Payment\Settings::titleB()
 * @used-by \Df\Payment\W\Action::notImplemented()
 * @used-by \Df\Payment\W\Exception::mTitle()
 * @used-by \Df\Payment\W\Handler::log()
 * @param string|object $c
 */
function dfpm_title($c):string {/** @var IM|M $m */ return dfp_my($m = dfpm($c)) ? $m->titleB() : df_module_name($m);}

/**
 * 2017-03-30
 * @used-by \Df\Payment\ConfigProvider::m()
 * @param IM|II|OP|QP|O|Q|T|object|string|null $c
 * @param mixed $s
 */
function dfpmq($c, $s = null):M {
	$r = dfpm($c); /** @var M $r */
	$r->setInfoInstance(dfp(df_quote()));
	if ($s) {
		$r->setStore(df_store_id($s));
	}
	return $r;
}