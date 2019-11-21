<?php
use Magento\Framework\Module\Manager as MM;
use Magento\Framework\Module\ModuleList as ML;
use Magento\Framework\Module\ModuleListInterface as IML;

/**
 * 2019-11-21
 * @param string $m
 * @return bool
 */
function df_module_enabled($m) {return df_module_m()->isEnabled($m);}

/**
 * 2017-06-21
 * @used-by \Dfr\Core\Console\Update::execute()
 * @param string $m
 * @return bool
 */
function df_module_exists($m) {return df_modules_o()->has($m);}

/**
 * 2019-11-21
 * @used-by df_module_enabled()
 * @return MM
 */
function df_module_m() {return df_o(MM::class);}

/**
 * 2017-04-01
 * @used-by df_module_exists()
 * @used-by dfe_modules()
 * @return IML|ML
 */
function df_modules_o() {return df_o(IML::class);}

/**
 * 2017-06-21
 * @used-by dfe_modules()
 * @used-by \Dfr\Core\Console\State::execute()
 * @used-by \Dfr\Core\Console\Update::execute()
 * @param string $p
 * @return string[]
 */
function df_modules_p($p) {return dfcf(function($p) {return df_sort_names(array_filter(
	df_modules_o()->getNames(), function($m) use($p) {return df_starts_with($m, $p);}
));}, [$p]);}