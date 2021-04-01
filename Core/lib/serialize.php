<?php
/**
 * 2016-07-18
 * Добавил вызов @uses df_check_json(),
 * потому что иначе для JSON кодирование с последующим декодированием даст некорректный результат:
 * кодирование оставит JSON неизменным, а декодирование сделает из JSON массив,
 * и получается, что после двух обратных операций изменился тип значения с JSON на массив.
 * @used-by df_cache_save()
 * @param mixed $v
 * @return string
 */
function df_serialize_simple($v) {return df_check_json($v) ? $v : json_encode($v);}

/**
 * @used-by df_cache_get_simple()
 * @param string|null $v
 * @return mixed|bool
 */
function df_unserialize_simple($v) {return df_json_decode($v);}