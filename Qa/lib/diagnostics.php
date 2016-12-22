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