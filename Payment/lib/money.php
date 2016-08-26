<?php
/**
 * 2016-08-26
 * https://3v4l.org/MnKRi
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