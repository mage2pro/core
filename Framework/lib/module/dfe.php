<?php
/**
 * 2017-05-05 It returns an array like [«Dfe_PortalStripe»]]].
 * 2017-06-19 I intentionally do not return the «Dfr_*» modules, because they are not extensions
 * (they are used for language translation).
 * @used-by dfe_packages()
 * @see df_modules_my()
 * @return string[]
 */
function dfe_modules():array {return df_modules_p('Dfe_');}

/**
 * 2017-04-01
 * Возвращает массив вида ['AllPay 1.5.3' => [информация из локального composer.json]].
 * Ключи массива не содержат приставку «Dfe_».
 * @used-by dfe_modules_log()
 * @used-by \Df\Core\Controller\Index\Index::execute()
 * @return array(string => array)
 */
function dfe_modules_info():array {return dfcf(function() {return df_map_kr(dfe_packages(), function($m, $p) {return [
	df_cc_s(substr($m, 4), $p['version']), $p
];});});};

/**
 * 2017-04-01
 * @used-by \Df\Sales\Observer\OrderPlaceAfter::execute()
 */
function dfe_modules_log():void {df_sentry(null
	,sprintf('%s: %s', df_domain_current(), df_csv_pretty(array_keys(dfe_modules_info())))
	# 2023-07-25
	# "Change the 3rd argument of `df_sentry` from `$context` to `$extra`": https://github.com/mage2pro/core/issues/249
	,['Backend URL' => df_url_backend_ns()]
);}