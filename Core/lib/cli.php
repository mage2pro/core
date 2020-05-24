<?php
/**
 * 2017-03-15 Нулевой параметр argv — это имя текущего скрипта.
 * @used-by df_cli_cmd()
 * @used-by df_cli_script()
 * @used-by df_is_cron()
 * @param int $i [optional]
 * @return string|string[]
 */
function df_cli_argv($i = null) {return dfa(dfa($_SERVER, 'argv', []), $i);}

/**
 * 2020-05-24
 * @used-by df_log_l()
 * @return string
 *
 */
function df_cli_cmd() {return df_cc_s(df_cli_argv());}

/**
 * 2020-02-15
 * @used-by df_is_bin_magento()
 * @return string
 */
function df_cli_script() {return df_cli_argv(0);}

/**
 * 2016-12-23 http://stackoverflow.com/a/7771601
 * @see \Magento\Framework\Shell::execute()
 * @used-by df_sentry_m()
 * @return string
 */
function df_cli_user() {return dfcf(function() {return exec('whoami');});}

/**
 * 2020-02-15
 * 1) `bin/magento` can be called with a path prefix, so I use @uses df_ends_with()
 * 2) df_cli_script() returns «bin/magento» even in the `php bin/magento ...` case.
 * @used-by df_is_cron()
 * @return bool
 */
function df_is_bin_magento() {return df_ends_with(df_cli_script(), 'bin/magento');}

/**
 * 2016-10-25 http://stackoverflow.com/a/1042533
 * @used-by df_action_name()
 * @used-by df_header_utf()
 * @used-by df_log_l()
 * @used-by df_sentry_m()
 * @used-by df_webserver()
 * @return bool
 */
function df_is_cli() {return 'cli' === php_sapi_name();}