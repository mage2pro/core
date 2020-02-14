<?php
/**
 * 2020-02-15
 * df_cli_argv(1) returns «cron:run» even in the `bin/magento cron:run --bootstrap=standaloneProcessStarted=1` case.
 * @used-by df_error()
 * @return bool
 */
function df_is_cron() {return df_is_bin_magento() && 'cron:run' === df_cli_argv(1);}