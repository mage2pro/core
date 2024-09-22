<?php
/**
 * http://darklaunch.com/2009/05/06/php-normalize-newlines-line-endings-crlf-cr-lf-unix-windows-mac
 * @used-by df_explode_n()
 */
function df_normalize(string $s):string {return strtr($s, ["\r\n" => "\n", "\r" => "\n"]);}