<?php
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
	$result = serialize($data);
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
	$result = @unserialize($data);
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


 