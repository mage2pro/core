<?php
/**
 * 2017-03-15 Нулевой параметр argv — это имя текущего скрипта.
 * 2022-11-23 With $i = null the function returns all `argv` data: @see df_cli_cmd().
 * @used-by df_cli_cmd()
 * @used-by df_cli_script()
 * @used-by df_is_cron()
 * @param int|null $i [optional]
 * @return string|string[]
 */
function df_cli_argv($i = null) {return dfa(dfa($_SERVER, 'argv', []), $i);}

/**
 * 2020-05-24
 * @used-by df_context()
 */
function df_cli_cmd():string {return df_cc_s(df_cli_argv());}

/**
 * 2020-02-15
 * @used-by df_is_bin_magento()
 */
function df_cli_script():string {return df_cli_argv(0);}

/**
 * 2016-12-23 http://stackoverflow.com/a/7771601
 * @see \Magento\Framework\Shell::execute()
 * @used-by df_sentry_m()
 */
function df_cli_user():string {return dfcf(function() {return exec('whoami');});}

/**
 * 2020-02-15
 * 1) `bin/magento` can be called with a path prefix, so I use @uses df_ends_with()
 * 2) df_cli_script() returns «bin/magento» even in the `php bin/magento ...` case.
 * @used-by df_is_cron()
 */
function df_is_bin_magento():bool {return df_ends_with(df_cli_script(), 'bin/magento');}

/**
 * 2016-10-25 http://stackoverflow.com/a/1042533
 * @used-by df_action_name()
 * @used-by df_context()
 * @used-by df_header_utf()
 * @used-by df_sentry_m()
 * @used-by df_webserver()
 * @used-by \Df\Sentry\Client::__construct()
 * @used-by \Df\Sentry\Client::capture()
 */
function df_is_cli():bool {return 'cli' === php_sapi_name();}