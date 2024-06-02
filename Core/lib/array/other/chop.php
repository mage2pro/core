<?php
/**
 * 2016-09-07
 * 2017-03-06 @uses mb_substr() корректно работает с $length = null.
 * 2022-11-23
 * If $length is 0, then @uses mb_substr() returns an empty string: https://3v4l.org/ijD3V
 * If $length is NULL, then @uses mb_substr() returns all characters to the end of the string.
 * https://3v4l.org/ijD3V
 * 2022-11-26 That is why I use @uses df_etn().
 * @used-by \Df\Payment\Charge::metadata()
 * @param string[] $a
 * @return string[]
 */
function dfa_chop(array $a, int $length = 0):array {return df_map('mb_substr', $a, [0, df_etn($length)]);}