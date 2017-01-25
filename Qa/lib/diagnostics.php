<?php
use Df\Core\Exception as DFE;

/**
 * 2015-04-05
 * @used-by \Df\Core\Exception_InvalidObjectProperty::__construct()
 * @used-by Df_Core_Validator::check()
 * @param mixed $value
 * @param bool $addQuotes [optional]
 * @return string
 */
function df_debug_type($value, $addQuotes = true) {
	/** @var string $result */
	if (is_object($value)) {
		$result = 'объект класса ' . get_class($value);
	}
	else if (is_array($value)) {
		$result = sprintf('массив с %d элементами', count($value));
	}
	else if (is_null($value)) {
		$result = 'NULL';
	}
	else {
		$result = sprintf('%s (%s)', df_string($value), gettype($value));
	}
	return !$addQuotes ? $result : df_quote_russian($result);
}

/**
 * 2017-01-25
 * @used-by \Dfe\CheckoutCom\Charge::metaData()
 * @used-by \Dfe\Klarna\UserAgent::__construct()
 * http://serverfault.com/a/164159
 * @param bool $asArray [optional]
 * @return string|string[]
 * An example of result: «Apache/2.4.20» or ['Apache', '2.4.20'].
 */
function df_webserver($asArray = false) {return dfcf(function($asArray = false) {
	/** @var string|null $s */
	// «Apache/2.4.20 (Win64) OpenSSL/1.0.2h PHP/5.6.22»
	$s = dfa($_SERVER, 'SERVER_SOFTWARE');
	/** @var string $result */
	$result = $s ? df_first(explode(' ', $s)) : (
		df_is_cli() ? 'PHP CLI/' . phpversion() : 'Unknown/Unknown'
	);
	return !$asArray ? $result : explode('/', $result);
}, func_get_args());}

