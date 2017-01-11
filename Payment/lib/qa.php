<?php
/**
 * 2016-07-14
 * Поддержка тегов HTML обеспечивается шаблоном Df_Checkout/messages
 * @param string|null $message [optional]
 * @return void
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
 * @param string|object $caller
 * @param string|mixed[] $data
 * @param string|null $suffix [optional]
 * @return void
 */
function dfp_log($caller, $data, $suffix = null) {
	/** @var string $method */
	$code = dfp_method_code($caller);
	$data = !is_array($data) ? $data : df_json_encode_pretty($data);
	df_report(df_ccc('--', "mage2.pro/$code-{date}--{time}", $suffix) .  '.log', $data);
}

/**
 * 2016-09-08
 * @param string|object $caller
 * @param string|mixed[] $data
 * @param string|null $suffix [optional]
 * @return void
 */
function dfp_report($caller, $data, $suffix = null) {
	/** @var string $title */
	$title = dfp_method_title($caller);
	/** @var string $json */
	/** @var array(string => mixed) $extra */
	if (!is_array($data)) {
		$json = $data;
		$extra = ['Payment Data' => $data];
	}
	else {
		$json = df_json_encode_pretty($data);
		/**
		 * 2017-01-03
		 * Этот полный JSON в конце массива может быть обрублен в интерфейсе Sentry
		 * (и, соответственно, так же обрублен при просмотре события в формате JSON
		 * по ссылке в шапке экрана события в Sentry),
		 * однако всё равно удобно видеть данные в JSON, пусть даже в обрубленном виде.
		 */
		$extra = $data + ['_json' => $json];
	}
	df_sentry(!$suffix ? $title : "[$title] $suffix", [
		'extra' => $extra, 'tags' => ['Payment Method' => $title]
	]);
	dfp_log($caller, $json, $suffix);
}