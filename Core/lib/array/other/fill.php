<?php
/**
 * Эта функция отличается от @uses array_fill() только тем,
 * что разрешает параметру $length быть равным нулю.
 * Если $length = 0, то функция возвращает пустой массив.
 * @uses array_fill() разрешает параметру $num (аналог $length)
 * быть равным нулю только начиная с PHP 5.6:
 * https://php.net/manual/function.array-fill.php
 * «5.6.0	num may now be zero. Previously, num was required to be greater than zero»
 * @see array_fill_keys()
 * @used-by df_vector_sum()
 * @param mixed $v
 */
function dfa_fill(int $startIndex, int $length, $v):array {return !$length ? [] : array_fill($startIndex, $length, $v);}