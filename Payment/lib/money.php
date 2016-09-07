<?php
/**
 * 2016-08-26
 * https://3v4l.org/MnKRi
 * 2016-09-07
 * Не знаю, что лучше: sprintf или number_format:
 * https://3v4l.org/N8p2G
 * number_format($value, $precision, '.', '')
 * @param float|int $value
 * @return string
 */
function df_2f($value) {return sprintf('%.2f', $value);}

/**
 * 2016-08-26
 * @param float|int|string $amount
 * @return string
 */
function dfp_last2($amount) {return substr(strval(round(100 * df_float($amount))), -2);}