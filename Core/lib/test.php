<?php
/**
 * 2016-11-17
 * В качестве $moduleName можно передавать:
 * 1) Имя модуля. «A_B»
 * 2) Имя класса. «A\B\C»
 * 3) Объект класса.
 *
 * @param string|object $moduleName
 * @param string $localPath [optional]
 * @return string
 */
function df_test_file($moduleName, $localPath = '') {return
	df_cc_path(df_module_dir($moduleName), 'T/data', $localPath)
;}

/**
 * 2016-11-21
 * @param string|object $moduleName
 * @param string $localPath [optional]
 * @return string
 */
function df_test_file_l($moduleName, $localPath = '') {return
	file_get_contents(df_test_file($moduleName, $localPath))
;}

/**
 * 2016-11-21
 * @param string|object $moduleName
 * @param string $localPath [optional]
 * @return array(string => string|array)
 */
function df_test_file_lj($moduleName, $localPath = '') {return df_json_decode(df_test_file_l(
	$moduleName, "$localPath.json"
));}


