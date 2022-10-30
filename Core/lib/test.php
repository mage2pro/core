<?php
/**
 * 2016-11-17
 * $m could be:
 * 1) a module name: «A_B»
 * 2) a class name: «A\B\C».
 * 3) an object: it comes down to the case 2 via @see get_class()
 * 4) `null`: it comes down to the case 1 with the «Df_Core» module name.
 * @used-by df_test_file_l()
 * @param string|object|null $m
 * @param string $localPath [optional]
 */
function df_test_file($m, $localPath = ''):string {return df_cc_path(df_module_dir($m), 'T/data', $localPath);}

/**
 * 2016-11-21
 * @used-by df_test_file_lj()
 * @param string|object $m
 * @param string $localPath [optional]
 */
function df_test_file_l($m, $localPath = ''):string {return file_get_contents(df_test_file($m, $localPath));}

/**
 * 2016-11-21
 * @used-by \Dfe\BlackbaudNetCommunity\Customer::p()
 * @param string|object $m
 * @param string $localPath [optional]
 * @return array(string => string|array)
 */
function df_test_file_lj($m, $localPath = ''):array {return df_json_decode(df_test_file_l($m, "$localPath.json"));}