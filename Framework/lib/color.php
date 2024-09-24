<?php
/**
 * 2019-08-21
 * 1) https://stackoverflow.com/a/15202130
 * 2) A $hex: «#ff9900».
 * @used-by Dfe\Color\Image::palette()
 * @return int[]
 */
function df_hex2rgb(string $hex):array {return sscanf($hex, "#%02x%02x%02x");}

/**
 * 2015-11-29
 * @uses dechex()
 * https://php.net/manual/function.dechex.php
 * http://stackoverflow.com/a/15202156
 * 2022-10-16 @deprecated It is unused.
 * @param int[] $rgb
 */
function df_rgb2hex(array $rgb):string {return df_pad0(6, implode(array_map('dechex', df_int($rgb))));}