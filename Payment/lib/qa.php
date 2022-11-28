<?php
use Df\Payment\Method as M;
use Magento\Payment\Model\MethodInterface as IM;

/**
 * 2016-07-14 Поддержка тегов HTML обеспечивается шаблоном Df_Checkout/messages
 * 2022-11-28 @deprecated It is unused.
 * @param string|null $m [optional]
 */
function dfp_error($m = null):void {df_checkout_error(dfp_error_message($m));}

/**
 * 2016-08-19
 * @used-by dfp_error()
 * @used-by \Df\Payment\PlaceOrderInternal::message()
 * @used-by \Dfe\Klarna\Exception::messageC()
 * @used-by \Dfe\Omise\Exception\Charge::messageC()
 * @used-by \Dfe\Stripe\Exception::messageC()
 * @used-by \Dfe\TwoCheckout\Exception::messageC()
 * @param string|null $m [optional]
 */
function dfp_error_message($m = ''):string {return nl2br(df_cc_n(
	__("Sorry, the payment attempt is failed.")
	, $m ? __("The payment service's message is «<b>%1</b>».", $m) : ''
	,__("Please try again, or try another payment method.")
));}

/**
 * 2016-09-08
 * @used-by \CanadaSatellite\Bambora\Action::check() (https://github.com/canadasatellite-ca/bambora)
 * @used-by \Df\GingerPaymentsBase\Init\Action::res()
 * @used-by \Df\Payment\CustomerReturn::execute()
 * @used-by \Df\Payment\Init\Action::action()
 * @used-by \Df\StripeClone\Method::transInfo()
 * @used-by \Dfe\AlphaCommerceHub\Method::transInfo()
 * @used-by \Dfe\CheckoutCom\Handler::p()
 * @used-by \Dfe\CheckoutCom\Handler\CustomerReturn::p()
 * @used-by \Dfe\CheckoutCom\Response::a()
 * @used-by \Dfe\Qiwi\Init\Action::preorder()
 * @used-by \Dfe\SecurePay\Refund::process()
 * @used-by \Dfe\TwoCheckout\Handler::p()
 * @param string|object $m
 * @param string|mixed[] $d
 */
function dfp_report($m, $d, string $title = '', string $suffix = ''):void {
	dfp_sentry_tags($m);
	df_sentry($m, $title ?: dfpm_title($m), ['extra' => is_array($d) ? $d : ['Payment Data' => $d]]);
	df_log_l(df_module_name_c($m), $d, $suffix);
}

/**
 * 2017-02-09
 * @used-by dfp_report()
 * @used-by \Df\Payment\W\Action::notImplemented()
 * @used-by \Df\Payment\Method::action()
 * @used-by \Df\Payment\W\Handler::log()
 * @param string|object $c
 */
function dfp_sentry_tags($c):void {
	$m = dfpm($c); /** @var IM|M $m */
	df_sentry_tags($m,
		[dfpm_title($m) => df_package_version($c)]
		+ (!dfp_my($m) ? [] : ['Payment Mode' => $m->test('development', 'production')])
	);
}