<?php
use Magento\Framework\App\ScopeInterface as IScope;
use Zend_Uri_Http as zUriH;

/**           
 * 2020-01-19
 * 2022-11-24 @deprecated It is unused.
 * @param null|string|int|IScope $s
 */
function df_replace_store_code_in_url($s, string $u = ''):string {
	$z = df_zuri($u ?: df_current_url()); /** @var zUriH $z */
	$z->setPath('/' . df_cc_path(
		df_store_code($s)
		,df_trim_ds_left(df_trim_text_left(df_trim_ds_left($z->getPath()), df_store_code_from_url($u)))
	));
	return $z->getUri();
}

/**
 * 2020-01-18
 * @used-by df_replace_store_code_in_url()
 * @param string|null $u [optional]
 * @return string|null
 */
function df_store_code_from_url($u = null) {
	$c = df_first(df_clean(df_explode_path(df_url_path($u ?: df_current_url())))); /** @var string $c */
	return !in_array($c, df_store_codes()) ? null : $c;
}