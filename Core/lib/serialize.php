<?php
if (false) {
	/**
	 * @param mixed $data
	 * @return string
	 */
	function igbinary_serialize($data) {df_should_not_be_here(__FUNCTION__);}

	/**
	 * @param string $data
	 * @return mixed|bool
	 */
	function igbinary_unserialize($data) {df_should_not_be_here(__FUNCTION__);}
}

/**
 * 2015-12-19
 * PHP 7.0.1 почему-то приводит к сбою при декодировании пустой строки:
 * «Decoding failed: Syntax error»
 * @param string|null $string
 * @param bool $throw [optional]
 * @return mixed|bool|null
 * @throws Exception
 * Returns the value encoded in json in appropriate PHP type.
 * Values true, false and null are returned as TRUE, FALSE and NULL respectively.
 * NULL is returned if the json cannot be decoded
 * or if the encoded data is deeper than the recursion limit.
 * http://php.net/manual/function.json-decode.php
 */
function df_json_decode($string, $throw = true) {
	/** @var mixed|bool|null $result */
	if ('' === $string || is_null($string)) {
		$result = $string;
	}
	else {
		$result = json_decode($string, true);
		if (is_null($result) && $throw) {
			df_assert_ne(JSON_ERROR_NONE, json_last_error());
			df_error(__("Parsing a JSON document failed with the message «%1».", json_last_error_msg()));
		}
	}
	return $result;
}

/** @return bool */
function df_igbinary_available() {
	static $r; return !is_null($r) ? $r : $r = false && function_exists('igbinary_serialize');
}

/**
 * @param mixed|\Df\Core\Serializable $data
 * @return string
 */
function df_serialize($data) {
	/** @var bool $supportsSerializableInterface */
	$supportsSerializableInterface = $data instanceof \Df\Core\Serializable;
	/** @var array(string => mixed) $container */
	if ($supportsSerializableInterface) {
		$container = $data->serializeBefore();
	}
	/** @var string $result */
	$result = df_igbinary_available() ? igbinary_serialize($data) : serialize($data);
	if ($supportsSerializableInterface) {
		$data->serializeAfter($container);
	}
	return $result;
}

/**
 * 2016-07-18
 * добавил вызов @uses df_check_json(),
 * потому что иначе для JSON кодирование с последующим декодированием даст некорректный результат:
 * кодирование оставит JSON неизменным, а декодирование сделает из JSON массив,
 * и получается, что после двух обратных операций изменился тип значения с JSON на массив.
 * @param mixed $data
 * @return string
 */
function df_serialize_simple($data) {return df_check_json($data) ? $data : json_encode($data);}

/**
 * @param string $data
 * @return mixed|\Df\Core\Serializable|bool
 */
function df_unserialize($data) {
	/** @var mixed|\Df\Core\Serializable|bool $result */
	$result = df_igbinary_available() ? @igbinary_unserialize($data) : @unserialize($data);
	if ($result instanceof \Df\Core\Serializable) {
		$result->unserializeAfter();
	}
	return $result;
}

/**
 * @param string $string
 * @return mixed|bool
 */
function df_unserialize_simple($string) {return df_json_decode($string);}


 