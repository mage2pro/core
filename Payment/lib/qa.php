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
 * 2017-01-11
 * @used-by \Df\Payment\W\Handler::log()
 * @param string|object $caller
 * @param string|mixed[] $data
 * @param string|null $suffix [optional]
 */
function dfp_log_l($caller, $data, $suffix = null) {
	/** @var string $method */
	$code = dfpm_code($caller);
	/** @var string $ext */
	list($ext, $data) = !is_array($data) ? ['log', $data] : ['json', df_json_encode_pretty($data)];
	df_report(df_ccc('--', "mage2.pro/$code-{date}--{time}", $suffix) .  ".$ext", $data);
}

/**
 * 2016-09-08
 * @param string|object $caller
 * @param string|mixed[] $data
 * @param string|null $suffix [optional]
 */
function dfp_report($caller, $data, $suffix = null) {
	/** @var string $title */
	$title = dfpm_title($caller);
	dfp_sentry_tags($caller);
	/** @var string $json */
	df_sentry($caller, !$suffix ? $title : "[$title] $suffix", ['extra' =>
		!is_array($data)
		? ['Payment Data' => $json = $data]
		// 2017-01-03
		// Этот полный JSON в конце массива может быть обрублен в интерфейсе Sentry
		// (и, соответственно, так же обрублен при просмотре события в формате JSON
		// по ссылке в шапке экрана события в Sentry),
		// однако всё равно удобно видеть данные в JSON, пусть даже в обрубленном виде.
		: $data + ['_json' => $json = df_json_encode_pretty($data)]
	]);
	dfp_log_l($caller, $json, $suffix);
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