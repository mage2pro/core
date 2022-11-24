<?php
/**
 * 2022-11-24
 * @used-by df_file_read()
 * @used-by df_http_get()
 * @used-by df_intl_dic_write()
 * @used-by df_module_enum()
 * @used-by df_request_body()
 * @used-by df_test_file_l()
 * @used-by \Df\GoogleFont\Font\Variant::ttfPath()
 * @used-by \Df\GoogleFont\Fonts\Png::contents()
 * @used-by \Dfe\Salesforce\Test\Basic::t02_the_latest_version()
 * @used-by \Dfe\Vantiv\Test\CaseT\Charge::req()
 * @param string $f
 * @param Closure|bool|mixed $onE [optional]
 * @param ?resource $rs [optional]
 */
function df_contents(string $f, $onE = true, $rs = null):string {return df_try(
	/**
	 * 2016-05-31
	 * file_get_contents() can raise @see E_WARNING:
	 * «failed to open stream: A connection attempt failed because the connected party did not properly respond
	 * after a period of time, or established connection failed because connected host has failed to respond.»
	 */
	function() use ($f, $rs):string {return df_assert_ne(false, @file_get_contents($f, null, $rs));}
	,true !== $onE ? $onE : function() use ($f) {df_error(
		'Unable to read the %s «%s».', df_check_url($f) ? 'URL' : 'file', $f
	);}
);}