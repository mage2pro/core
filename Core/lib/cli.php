<?php
/**
 * 2017-03-15
 * Нулевой параметр argv — это имя текущего скрипта.
 * @used-by df_sentry()
 * @return string[]
 */
function df_cli_argv() {return array_slice(dfa($_SERVER, 'argv', []), 1);}

/**
 * 2016-12-23
 * http://stackoverflow.com/a/7771601
 * @see \Magento\Framework\Shell::execute()
 * @return string
 */
function df_cli_user() {return dfcf(function() {return exec('whoami');});}

/**
 * 2016-10-25
 * http://stackoverflow.com/a/1042533
 * @used-by df_sentry()
 * @return bool
 */
function df_is_cli() {return 'cli' === php_sapi_name();}
