<?php
use Df\Payment\Operation as Op;
/**
 * 2016-08-27
 * @used-by \Df\Payment\Charge::customerReturn()
 * @param string|object $m
 * @return string
 */
function dfp_url_customer_return($m) {return df_url_frontend(df_route($m, 'customerReturn'));}

/**
 * 2016-08-27
 * @used-by \Df\Payment\Charge::customerReturnRemote()
 * @param string|object $m
 * @return string
 */
function dfp_url_customer_return_remote($m) {return df_webhook($m, 'customerReturn');}

/**
 * 2017-08-23
 * @used-by \Df\Payment\Operation::customerReturnRemoteWithFailure()
 * @param string|object $m
 * @return string
 */
function dfp_url_customer_return_remote_f($m) {return df_webhook($m, 'customerReturn', false, null, [
	Op::FAILURE => true
]);}