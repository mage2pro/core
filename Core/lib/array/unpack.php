<?php
/**
 * 2022-11-26
 * 1) https://3v4l.org/ovSu2
 * 2) It is similar to @see dfa_unpack(), but df_arg() does not call dfa_flatten().
 * 3)
 * 		[$v] => $v
 * 		[[$v]] => [$v]
 * 		[[$v1, $v2]] => [$v1, $v2]
 * 		[$v1, $v2] => [$v1, $v2]
 * 		[$v1, $v2, [$v3]] => [$v1, $v2, [$v3]] - The difference from @see dfa_unpack()
 * @see df_args()
 * @see dfa_unpack()
 * @return mixed|mixed[]
 */
function df_arg(array $a) {return isset($a[0]) && !isset($a[1]) ? $a[0] : $a;}

/**
 * 2015-12-25
 * Этот загадочный метод призван заменить код вида: `is_array($a) ? $a : func_get_args()`.
 * Теперь можно писать так: df_args(func_get_args()).
 * @see df_arg()
 * @see dfa_unpack()
 * @used-by df_clean()
 * @used-by df_clean_keys()
 * @used-by df_csv()
 * @used-by df_csv_pretty_quote()
 * @used-by df_format()
 * @used-by dfa_combine_self()
 * @used-by dfa_unset()
 * @see dfa_unpack()
 */
function df_args(array $a):array {return !$a || !is_array($a[0]) ? $a : $a[0];}

/**
 * 2020-01-29
 * 2022-11-26 It is similar to @see df_arg(), but dfa_unpack() also calls dfa_flatten().
 * @see df_arg()
 * @see df_args()
 * 		[$v] => $v
 * 		[[$v]] => [$v]
 * 		[[$v1, $v2]] => [$v1, $v2]
 * 		[$v1, $v2] => [$v1, $v2]
 * 		[$v1, $v2, [$v3]] => [$v1, $v2, $v3] - The difference from @see df_arg()
 * @used-by dfp_iia()
 * @return mixed|mixed[]
 */
function dfa_unpack(array $a) {return !($c = count($a)) ? null : (1 === $c ? $a[0] : dfa_flatten($a));}
