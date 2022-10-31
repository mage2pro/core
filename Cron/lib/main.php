<?php
/**
 * 2020-02-15 df_cli_argv(1) returns «cron:run» even in the `bin/magento cron:run --bootstrap=standaloneProcessStarted=1` case.
 * 2021-03-23
 * A shorter solution would be df_area_code_is('crontab'),
 * but area code can be emulated: @see \Magento\Framework\App\State::emulateAreaCode()
 * @used-by df_error()
 * @used-by \MageWorx\OptionBase\Helper\CustomerVisibility::isVisibilityFilterRequired() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/35)
 */
function df_is_cron():bool {return df_is_bin_magento() && 'cron:run' === df_cli_argv(1);}