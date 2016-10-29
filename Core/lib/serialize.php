<?php
/**
 * @param mixed $data
 * @return string
 */
function df_serialize($data) {return serialize($data);}

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
 * @return mixed|bool
 */
function df_unserialize($data) {return @unserialize($data);}

/**
 * @param string|null $v
 * @return mixed|bool
 */
function df_unserialize_simple($v) {return df_json_decode($v);}