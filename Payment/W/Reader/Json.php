<?php
namespace Df\Payment\W\Reader;
/**
 * 2017-03-09
 * @see \Df\GingerPaymentsBase\W\Reader
 * @see \Dfe\Omise\W\Reader
 * @see \Dfe\Paymill\W\Reader
 * @see \Dfe\Stripe\W\Reader
 */
abstract class Json extends \Df\Payment\W\Reader {
	/**
	 * 2017-01-04
	 * 2017-01-07 На localhost результатом будет пустой массив.
	 * @override
	 * @see \Df\Payment\W\Reader::http()
	 * @used-by \Df\Payment\W\Reader::__construct()
	 * @return array(string => mixed)
	 */
	final protected function http() {return df_request_body_json();}
}