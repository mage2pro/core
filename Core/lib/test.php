<?php
/**
 * 2016-11-17
 * $m could be:
 * 1) A module name: «A_B»
 * 2) A class name: «A\B\C».
 * 3) An object: it comes down to the case 2 via @see get_class()
 * 4) `null`: it comes down to the case 1 with the «Df_Core» module name.
 * @param string|object|null $m
 * @param string $localPath [optional]
 * @return string
 */
function df_test_file($m, $localPath = '') {return df_cc_path(df_module_dir($m), 'T/data', $localPath);}

/**
 * 2016-11-21
 * @param string|object $m
 * @param string $localPath [optional]
 * @return string
 */
function df_test_file_l($m, $localPath = '') {return file_get_contents(df_test_file($m, $localPath));}

/**
 * 2016-11-21
 * @param string|object $m
 * @param string $localPath [optional]
 * @return array(string => string|array)
 */
function df_test_file_lj($m, $localPath = '') {return df_json_decode(df_test_file_l($m, "$localPath.json"));}