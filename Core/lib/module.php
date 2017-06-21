<?php
use Magento\Framework\Module\ModuleList as ML;
use Magento\Framework\Module\ModuleListInterface as IML;

/**
 * 2017-04-01
 * @used-by dfe_modules()
 * @return IML|ML
 */
function df_modules_o() {return df_o(IML::class);}

/**
 * 2017-06-21
 * @used-by dfe_modules()
 * @param string $p
 * @return string[]
 */
function df_modules_p($p) {return dfcf(function($p) {return df_sort_names(array_filter(
	df_modules_o()->getNames(), function($m) use($p) {return df_starts_with($m, $p);}
));}, [$p]);}

/**
 * 2017-05-05 It returns an array like [«Dfe_PortalStripe»]]].
 * 2017-06-19 I intentionally do not return the «Dfr_*» modules, because they are not extensions
 * (they are used for language translation).
 * @used-by dfe_packages()
 * @return string[]
 */
function dfe_modules() {return df_modules_p('Dfe_');}

/**
 * 2017-04-01
 * Возвращает массив вида ['AllPay 1.5.3' => [информация из локального composer.json]].
 * Ключи массива не седержат приставку «Dfe_».
 * @used-by dfe_modules_log()
 * @used-by \Df\Core\Controller\Index\Index::execute()
 * @return array(string => array)
 */
function dfe_modules_info() {return dfcf(function() {return df_map_kr(dfe_packages(), function($m, $p) {return [
	df_cc_s(substr($m, 4), $p['version']), $p
];});});};

/**
 * 2017-04-01
 * @used-by \Df\Sales\Observer\OrderPlaceAfter::execute()
 */
function dfe_modules_log() {df_sentry(null, sprintf('%s: %s', df_domain_current(), df_csv_pretty(array_keys(
	dfe_modules_info()
))));}