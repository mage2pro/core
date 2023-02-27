<?php
/**
 * 2022-11-24
 * @used-by df_file_read()
 * @used-by df_http_get()
 * @used-by df_intl_dic_write()
 * @used-by df_module_enum()
 * @used-by df_request_body()
 * @used-by df_test_file_l()
 * @used-by \Dfe\GoogleFont\Font\Variant::ttfPath()
 * @used-by \Dfe\GoogleFont\Fonts\Png::contents()
 * @used-by \Dfe\Salesforce\Test\Basic::t02_the_latest_version()
 * @used-by \Dfe\Vantiv\Test\CaseT\Charge::req()
 * @param string $f
 * @param Closure|bool|mixed $onE [optional]
 * @param ?resource $rs [optional]
 */
function df_contents(string $f, $onE = true, $rs = null):string {return df_try(
	/**
	 * 2015-11-27
	 * Обратите внимание, что для использования @uses file_get_contents
	 * с адресами https требуется расширение php_openssl интерпретатора PHP,
	 * однако оно является системным требованием Magento 2:
	 * http://devdocs.magento.com/guides/v2.0/install-gde/system-requirements.html#required-php-extensions
	 * Поэтому мы вправе использовать здесь @uses file_get_contents
	 * 2016-05-31, 2022-10-14
	 * file_get_contents() could generate @see E_WARNING: e.g.:
	 * 	*) if the file is absent
	 * 	*)  in the case of network errors:
	 * 			«failed to open stream: A connection attempt failed
	 * 			because the connected party did not properly respond after a period of time,
	 * 			or established connection failed because connected host has failed to respond.»
	 * https://www.php.net/manual/function.file-get-contents.php#refsect1-function.file-get-contents-errors
	 * That is why I use the silence operator.
	 */
	function() use ($f, $rs):string {return df_assert_ne(false, @file_get_contents($f, null, $rs));}
	,true !== $onE ? $onE : function() use ($f) {df_error(
		'Unable to read the %s «%s».', df_check_url($f) ? 'URL' : 'file', $f
	);}
);}