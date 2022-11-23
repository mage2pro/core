<?php
/**
 * 2015-11-28 http://stackoverflow.com/a/10368236
 * @used-by df_asset_create()
 * @used-by df_file_ext_def()
 * @used-by df_img_is_jpeg()
 * @used-by \TFC\Image\Command\C1::scan()
 */
function df_file_ext(string $f):string {return pathinfo($f, PATHINFO_EXTENSION);}

/**
 * 2020-06-28
 * @see df_strip_ext()
 * @used-by df_block()
 * @used-by df_module_file()
 */
function df_file_ext_add(string $f, string $ext = ''):string {return !$ext ? $f : df_append($f, ".$ext");}

/**
 * 2018-07-06
 * @used-by df_report()
 */
function df_file_ext_def(string $f, string $ext):string {return ($e = df_file_ext($f)) ? $f : df_trim_right($f, '.') . ".$ext";}

/**
 * 2015-04-01
 * 2019-08-09
 * 1) `preg_replace('#\.[^.]*$#', '', $file)` preserves the full path.
 * 2) `pathinfo($file, PATHINFO_FILENAME)` (https://stackoverflow.com/a/22537165)
 * strips the full path and returns the base name only.
 * @see df_file_ext_add()
 * @used-by wolf_u2n()
 * @param string $s
 * @return string|null
 */
function df_strip_ext($s) {return preg_replace('#\.[^.]*$#', '', $s);}