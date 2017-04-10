<?php
use Magento\Framework\Module\ModuleList as ML;
use Magento\Framework\Module\ModuleListInterface as IML;
/**
 * 2017-04-01
 * Возвращает массив вида ['AllPay 1.5.3' => [информация из локального composer.json]].
 * Ключи массива не седержат приставку «Dfe_».
 * @used-by df_modules_log()
 * @used-by \Df\Core\Controller\Index\Index::execute()
 * @return array(string => array)
 */
function df_modules() {return dfcf(function() {return df_map_r(
	df_sort_names(array_filter(df_modules_o()->getNames(), function($m) {return
		df_starts_with($m, 'Dfe_')
	;})), function($m) {$c = df_package($m); return [df_cc_s(substr($m, 4), $c['version']), $c];}
);});};

/**
 * 2017-04-01
 * @used-by \Df\Sales\Observer\OrderPlaceAfter::execute()
 */
function df_modules_log() {df_sentry(null, sprintf('%s: %s', df_domain(), df_csv_pretty(array_keys(
	df_modules()
))));}

/**
 * 2017-04-01
 * @used-by df_modules()
 * @return IML|ML
 */
function df_modules_o() {return df_o(IML::class);}