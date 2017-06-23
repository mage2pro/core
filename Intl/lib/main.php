<?php
/**
 * 2017-06-23
 * @used-by df_intl_dic_read()
 * @param string|object $m
 * @param string|null $folder [optional]
 * @param string|null $locale [optional]
 * @return array(string => string)|mixed
 */
function df_intl_dic_path($m, $locale = null, $folder = null) {return df_cc_path(
	df_module_dir($m), $folder ?: 'i18n', ($locale ?: df_locale()) . '.csv'
);}

/**
 * 2017-06-14 How to parse a CSV file? https://mage2.pro/t/4063
 * @used-by \Df\Intl\Js::_toHtml()
 * @used-by \Dfr\Core\Console\State::execute()
 * @used-by \Dfr\Core\Console\Update::execute()
 * @param string|object $m
 * @param string|null $folder [optional]
 * @param string|null $locale [optional]
 * @return array(string => string)|mixed
 */
function df_intl_dic_read($m, $locale = null, $folder = null) {
	$p = df_intl_dic_path($m, $locale, $folder);
	return df_try(function() use($p) {return df_csv_o()->getDataPairs($p);}, [])
;}