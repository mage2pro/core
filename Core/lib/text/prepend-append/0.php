<?php
/**
 * 2015-11-29 Добавляет к строковому представлению целого числа нули слева.
 * 2015-12-01
 * Строковое представление может быть 16-ричным (код цвета), поэтому убрал @see df_int()
 * http://stackoverflow.com/a/1699980
 * @used-by df_rgb2hex()
 */
function df_pad0(int $length, string $number):string {return str_pad($number, $length, '0', STR_PAD_LEFT);}