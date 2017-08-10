<?php
use Df\Core\Exception as DFE;

/**
 * 2017-04-03
 * @param bool $cond [optional]
 */
function df_break($cond = true) {$cond && function_exists('xdebug_break') ? xdebug_break() : null;}

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
		$result = 'object of class ' . get_class($value);
	}
	elseif (is_array($value)) {
		$result = sprintf('an array with %d elements', count($value));
	}
	elseif (is_null($value)) {
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
 * @used-by \Dfe\Klarna\Api\Checkout\V3\UserAgent::__construct()
 * «How to detect the current web server programmatically?» https://mage2.pro/t/2523
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
	$result = $s ? df_first(explode(' ', $s)) : (df_is_cli() ? 'PHP CLI/' . phpversion() : 'Unknown/Unknown');
	return !$asArray ? $result : explode('/', $result);
}, func_get_args());}