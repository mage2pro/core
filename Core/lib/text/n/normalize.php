<?php
/**
 * http://darklaunch.com/2009/05/06/php-normalize-newlines-line-endings-crlf-cr-lf-unix-windows-mac
 * @used-by df_explode_n()
 * @used-by df_is_multiline()
 * @used-by df_single_line()
 * @used-by df_string()
 * @used-by Df\Qa\Dumper::dumpS()
 */
function df_normalize(string $s):string {return strtr($s, ["\r\n" => "\n", "\r" => "\n"]);}