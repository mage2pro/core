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
 * @param string $string
 * @param bool $returnArray [optional]
 * @return mixed|bool
 */
function df_json_decode($string, $returnArray = true) {
	return '' === $string ? $string : json_decode($string, $returnArray);
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
 * @param mixed $data
 * @return string
 */
function df_serialize_simple($data) {return json_encode($data);}

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


 