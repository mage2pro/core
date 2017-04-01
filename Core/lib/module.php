<?php
use Magento\Framework\Module\ModuleList as ML;
use Magento\Framework\Module\ModuleListInterface as IML;
/**
 * 2017-04-01
 * @used-by df_modules_log()
 * @return IML|ML
 */
function df_module_list() {return df_o(IML::class);}

/**
 * 2017-04-01
 * @used-by \Df\Sales\Observer\OrderPlaceAfter::execute()
 * @param mixed $s [optional]
 */
function df_modules_log($s = null) {df_sentry(null, sprintf('%s: %s', df_domain($s),
	df_csv_pretty(df_map_k(function($k, array $v) {return sprintf(
		'%s %s', substr($k, 4), $v['setup_version']
	);}, df_modules_my(true)))
));}

/**
 * 2017-04-01
 * @used-by df_modules_log()
 * @used-by \Df\Core\Controller\Index\Index::execute()
 * @param bool $full
 * @return array
 */
function df_modules_my($full = false) {return dfcf(function($full = false) {
	$ml = df_module_list();
	/**
	 * @var \Closure $filter
	 * @return bool
	 */
	$filter = function($name) {return df_starts_with($name, 'Dfe_');};
	return $full
		? df_ksort(array_filter($ml->getAll(), $filter, ARRAY_FILTER_USE_KEY))
		: df_sort_names(array_filter($ml->getNames(), $filter))
	;
}, [$full]);}