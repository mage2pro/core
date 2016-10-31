<?php
/**
 * 2016-07-18
 * Видел решение здесь: http://stackoverflow.com/a/6041773
 * Но оно меня не устроило.
 * И без собаки будет Warning.
 * @param mixed $value
 * @return bool
 */
function df_check_json($value) {return !is_null(@json_decode($value));}

/**
 * 2016-08-19
 * @see json_decode() спокойно принимает не только строки, но и числа, а также true.
 * Наша функция возвращает true, если аргумент является именно строкой.
 * @param mixed $v
 * @return bool
 */
function df_check_json_complex($v) {
	return is_string($v) && df_starts_with($v, '{') && df_check_json($v);
}

/**
 * @param $s|null $string
 * @param bool $throw [optional]
 * @return mixed|bool|null
 * @throws Exception
 * Returns the value encoded in json in appropriate PHP type.
 * Values true, false and null are returned as TRUE, FALSE and NULL respectively.
 * NULL is returned if the json cannot be decoded
 * or if the encoded data is deeper than the recursion limit.
 * http://php.net/manual/function.json-decode.php
 */
function df_json_decode($s, $throw = true) {
	/** @var mixed|bool|null $result */
	/**
	 * 2015-12-19
	 * PHP 7.0.1 почему-то приводит к сбою при декодировании пустой строки:
	 * «Decoding failed: Syntax error»
	 */
	if ('' === $s || is_null($s)) {
		$result = $s;
	}
	else {
		/**
		 * 2016-10-30
		 * json_decode('7700000000000000000000000') возвращает 7.7E+24
		 * https://3v4l.org/NnUhk
		 * http://stackoverflow.com/questions/28109419
		 * Такие длинные числоподобные строки используются как идентификаторы КЛАДР
		 * (модулем доставки «Деловые Линии»), и поэтому их нельзя так корёжить.
		 * Поэтому используем константу JSON_BIGINT_AS_STRING
		 * https://3v4l.org/vvFaF
		 */
		$result = json_decode($s, true, 512, JSON_BIGINT_AS_STRING);
		/**
		 * 2016-10-28
		 * json_encode(null) возвращает строку 'null',
		 * а json_decode('null') возвращает null.
		 * Добавил проверку для этой ситуации, чтобы не считать её сбоем.
		 */
		if (is_null($result) && 'null' !== $s && $throw) {
			df_assert_ne(JSON_ERROR_NONE, json_last_error());
			df_error(
				"Parsing a JSON document failed with the message «%s».\nThe document:\n{$s}"
				,json_last_error_msg()
			);
		}
	}
	return $result;
}

/**
 * 2015-12-09
 * @param mixed $data
 * @return string
 */
function df_json_encode($data) {return df_is_dev() ? df_json_encode_pretty($data) : json_encode($data);}

/**
 * 2016-10-31
 * Портировал из Российской сборки Magento:
 * https://code.dmitry-fedyuk.com/rm/2/blob/2.49.11/app/code/local/Df/Core/Helper/Output.php#L63
 * Df_Core_Helper_Output::json
 * @param mixed $data
 * @return string
 */
function df_json_encode_js($data) {return json_encode($data,
	JSON_FORCE_OBJECT
	|JSON_NUMERIC_CHECK
	|JSON_PRETTY_PRINT
	|JSON_UNESCAPED_SLASHES
	|JSON_UNESCAPED_UNICODE
);}

/**
 * 2015-12-06
 * @param mixed $data
 * @return string
 */
function df_json_encode_pretty($data) {return json_encode($data,
	JSON_PRETTY_PRINT
	|JSON_UNESCAPED_SLASHES
	|JSON_UNESCAPED_UNICODE
);}

/**
 * @see df_xml_prettify()
 * @param string $value
 * @return string
 */
function df_json_prettify($value) {return Zend_Json::prettyPrint(df_t()->adjustCyrillicInJson($value));}