<?php
/**
 * 2015-03-13
 * Отсекает последний элемент массива и возвращает «голову» (массив оставшихся элементов).
 * Похожая системная функция @see array_pop() возвращает отсечённый последний элемент.
 * Противоположная системная функция @see df_tail() отсекает первый элемент массива.
 * @used-by \Df\Config\Comment::groupPath()
 * @used-by \Df\Config\Source::sibling()
 * @used-by \Mineralair\Core\Controller\Modal\Index::execute()
 */
function df_head(array $a):array {return array_slice($a, 0, -1);}

/**
 * 2021-10-05, 2021-11-30
 * @uses array_slice() returns an empty array if `$limit` is `0`, and returns all elements if `$limit` is `null`,
 * so I convert `0` and other empty values to `null`.
 * @used-by df_bt()
 * @used-by df_product_images_additional()
 * @used-by \Df\Qa\Trace\Frame::url()
 */
function df_slice(array $a, int $offset, int $length = 0):array {return array_slice($a, $offset, df_etn($length));}

/**
 * Отсекает первый элемент массива и возвращает хвост (аналог CDR в Lisp).
 * Обратите внимание, что если исходный массив содержит меньше 2 элементов, то функция вернёт пустой массив.
 * @see df_first()
 * @see df_last()
 * @used-by df_error_create()
 * @used-by df_sprintf_strict()
 * @used-by df_zf_http_last_req()
 * @used-by \Df\Core\Text\Regex::match()
 * @used-by \Doormall\Shipping\Partner\Entity::locations()
 */
function df_tail(array $a):array {return array_slice($a, 1);}