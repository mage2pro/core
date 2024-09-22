<?php
/**
 * 2015-11-29 It prepends $s with $count zeros.
 * 2015-12-01
 * Строковое представление может быть 16-ричным (код цвета), поэтому убрал @see df_int()
 * http://stackoverflow.com/a/1699980
 * @used-by df_rgb2hex()
 */
function df_pad0(int $count, string $s):string {return str_pad($s, $count, '0', STR_PAD_LEFT);}