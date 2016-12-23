<?php
/**
 * 2016-10-25
 * http://stackoverflow.com/a/1042533
 * @return bool
 */
function df_is_cli() {return 'cli' === php_sapi_name();}

/**
 * 2016-12-23
 * http://stackoverflow.com/a/7771601
 * @see \Magento\Framework\Shell::execute()
 * @return string
 */
function df_cli_user() {return dfcf(function() {return exec('whoami');});}