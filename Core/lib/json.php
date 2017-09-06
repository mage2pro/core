<?php
/**
 * 2016-07-18
 * Видел решение здесь: http://stackoverflow.com/a/6041773
 * Но оно меня не устроило.
 * И без собаки будет Warning.
 * @param mixed $v
 * @return bool
 */
function df_check_json($v) {/** @noinspection PhpUsageOfSilenceOperatorInspection */ return !is_null(
	@json_decode($v)
);}

/**
 * 2016-08-19
 * @see json_decode() спокойно принимает не только строки, но и числа, а также true.
 * Наша функция возвращает true, если аргумент является именно строкой.
 * @param mixed $v
 * @return bool
 */
function df_check_json_complex($v) {return is_string($v) && df_starts_with($v, '{') && df_check_json($v);}

/**
 * @used-by df_credentials()
 * @used-by df_json_prettify()
 * @used-by \Df\API\Client::resJson()
 * @param $s|null $string
 * @param bool $throw [optional]
 * @return array|mixed|bool|null
 * @throws Exception
 * Returns the value encoded in json in appropriate PHP type.
 * Values true, false and null are returned as TRUE, FALSE and NULL respectively.
 * NULL is returned if the json cannot be decoded
 * or if the encoded data is deeper than the recursion limit.
 * http://php.net/manual/function.json-decode.php
 */
function df_json_decode($s, $throw = true) {
	/** @var mixed|bool|null $r */
	// 2015-12-19
	// PHP 7.0.1 почему-то приводит к сбою при декодировании пустой строки:
	// «Decoding failed: Syntax error»
	if ('' === $s || is_null($s)) {
		$r = $s;
	}
	else {
		// 2016-10-30
		// json_decode('7700000000000000000000000') возвращает 7.7E+24
		// https://3v4l.org/NnUhk
		// http://stackoverflow.com/questions/28109419
		// Такие длинные числоподобные строки используются как идентификаторы КЛАДР
		// (модулем доставки «Деловые Линии»), и поэтому их нельзя так корёжить.
		// Поэтому используем константу JSON_BIGINT_AS_STRING
		// https://3v4l.org/vvFaF
		$r = json_decode($s, true, 512, JSON_BIGINT_AS_STRING);
		// 2016-10-28
		// json_encode(null) возвращает строку 'null',
		// а json_decode('null') возвращает null.
		// Добавил проверку для этой ситуации, чтобы не считать её сбоем.
		if (is_null($r) && 'null' !== $s && $throw) {
			df_assert_ne(JSON_ERROR_NONE, json_last_error());
			df_error(
				"Parsing a JSON document failed with the message «%s».\nThe document:\n{$s}"
				,json_last_error_msg()
			);
		}
	}
	return df_json_sort($r);
}

/**
 * 2015-12-06
 * @used-by df_json_prettify()
 * @used-by \Df\ZohoBI\API\Validator::rs()
 * @used-by \Dfe\Dynamics365\API\Validator\JSON::rs()
 * @param mixed $v
 * @return string
 */
function df_json_encode($v) {return json_encode(df_json_sort($v),
	JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE
);}

/**
 * 2017-07-05
 * @param string|array(string => mixed) $j
 * @return string
 */
function df_json_prettify($j) {return df_json_encode(df_json_decode($j));}

/**
 * 2017-09-07
 * I use the @uses df_is_assoc() check,
 * because otherwise @uses df_ksort_r_ci() will convert the numeric arrays to associative ones,
 * and their numeric keys will be ordered as strings.
 * @used-by df_json_decode()
 * @used-by df_json_encode()
 * @param mixed $v
 * @return mixed
 */
function df_json_sort($v) {return !is_array($v) ? $v : (df_is_assoc($v) ? df_ksort_r_ci($v) : df_sort($v));}