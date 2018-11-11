<?php
use Df\Payment\Method as M;
/**
 * 2016-07-14
 * Поддержка тегов HTML обеспечивается шаблоном Df_Checkout/messages
 * @param string|null $message [optional]
 */
function dfp_error($message = null) {df_checkout_error(dfp_error_message($message));}

/**
 * 2016-08-19
 * @used-by dfp_error()
 * @used-by \Df\Payment\PlaceOrderInternal::message()
 * @used-by \Dfe\Stripe\Exception::messageC()
 * @param string|null $message [optional]
 * @return string
 */
function dfp_error_message($message = null) {return nl2br(df_cc_n(
	__("Sorry, the payment attempt is failed.")
	, $message ? __("The payment service's message is «<b>%1</b>».", $message) : null
	,__("Please try again, or try another payment method.")
));}

/**
 * 2016-09-08
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
 * @param string|object $caller
 * @param string|mixed[] $data
 * @param string|null $suffix [optional]
 */
function dfp_report($caller, $data, $suffix = null) {
	$title = dfpm_title($caller); /** @var string $title */
	dfp_sentry_tags($caller); /** @var string $json */
	df_sentry($caller, !$suffix ? $title : "[$title] $suffix", ['extra' =>
		is_array($data) ? $data : ['Payment Data' => $data]
	]);
	df_log_l($caller, $data, $suffix);
}

/**
 * 2017-02-09
 * @used-by dfp_report()
 * @used-by \Df\Payment\W\Action::notImplemented()
 * @used-by \Df\Payment\Method::action()
 * @used-by \Df\Payment\W\Handler::log()
 * @param string|object $c
 */
function dfp_sentry_tags($c) {$m = dfpm($c); df_sentry_tags($m, [
	$m->titleB() => df_package_version($c), 'Payment Mode' => $m->test('development', 'production')
]);}