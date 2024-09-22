<?php
/**
 * 2015-03-03
 * 2015-07-07
 * 1) Раньше алгоритм был таким: `strtr($text, "\r\n", '  ')`.
 * Однако он не совсем правилен, потому что если перенос строки записан в формате Windows
 * (то есть, в качестве переноса строки используется последовательность \r\n),
 * то прошлый алгоритм заменит эту последовательность на 2 пробела, а надо — на один:
 * «If given three arguments,
 * this function returns a copy of str where all occurrences of each (single-byte) character in from
 * have been translated to the corresponding character in to,
 * i.e., every occurrence of $from[$n] has been replaced with $to[$n],
 * where $n is a valid offset in both arguments.
 * If from and to have different lengths,
 * the extra characters in the longer of the two are ignored.
 * The length of str will be the same as the return value's.»
 * https://php.net/strtr
 * Новый алгоритм взял отсюда:  http://stackoverflow.com/a/20717751
 * 2021-12-13 @deprecated It is unused.
 */
function df_single_line(string $s):string {return str_replace(["\n", "\t"], ' ', df_normalize($s));}