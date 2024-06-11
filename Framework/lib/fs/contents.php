<?php
/**
 * 2022-11-24
 * 2023-07-26 "Unify `df_contents` and `df_file_read`": https://github.com/mage2pro/core/issues/275
 * 2024-06-11 "Improve `df_contents()`": https://github.com/mage2pro/core/issues/425
 * @used-by df_http_get()
 * @used-by df_intl_dic_write()
 * @used-by df_json_file_read()
 * @used-by df_magento_version_remote()
 * @used-by df_module_enum()
 * @used-by df_module_name_by_path()
 * @used-by df_package()
 * @used-by df_request_body()
 * @used-by df_test_file_l()
 * @used-by \Dfe\CheckoutCom\Controller\Index\Index::webhook()
 * @used-by \Dfe\Color\Image::dominant()
 * @used-by \Dfe\GoogleFont\Font\Variant::ttfPath()
 * @used-by \Dfe\GoogleFont\Fonts\Png::contents()
 * @used-by \Dfe\GoogleFont\Fonts\Sprite::datumPoints()
 * @used-by \Dfe\Salesforce\Test\Basic::t02_the_latest_version()
 * @used-by \Dfe\Vantiv\Test\CaseT\Charge::req()
 * @param Closure|bool|mixed $onE [optional]
 * @param ?resource $rs [optional]
 */
function df_contents(string $f, $onE = true, $rs = null):string {
	/**
	 * 2023-07-26
	 * 1) "`df_contents()` should accept internal paths": https://github.com/mage2pro/core/issues/273
	 * 2) df_is_url('php://input') returns `true`:
	 * https://github.com/mage2pro/core/issues/277
	 * https://3v4l.org/mTt87
	 * 2024-06-11
	 * I do not use @see is_file() because in can return `true` for an URL:
 	 * 		«As of PHP 5.0.0, this function can also be used with some URL wrappers»
	 * https://www.php.net/manual/en/function.is-file.php#refsect1-function.is-file-notes
	 * @var bool $isURL
	 */
	if (!($isURL = df_is_url($f))) {
		$f = df_path_abs($f);
	}
	return df_try(
		function() use ($f, $isURL, $rs):string {return df_assert_ne(false,
			/**
			 * 2015-11-27
			 * Обратите внимание, что для использования @uses file_get_contents()
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
			 * https://php.net/manual/function.file-get-contents.php#refsect1-function.file-get-contents-errors
			 * That is why I use the silence operator.
			 * 2023-07-16
			 * «file_get_contents(): Passing null to parameter #2 ($use_include_path) of type bool is deprecated
			 * in vendor/mage2pro/core/Framework/lib/fs/contents.php on line 36»:
			 * https://github.com/mage2pro/core/issues/230
			 * 2024-06-11
			 * 1) "Improve `df_contents()`": https://github.com/mage2pro/core/issues/425
			 * 2) "«PHP Warning: file_get_contents(vendor/mage2pro/core/<…>/composer.json):
			 * failed to open stream: No such file or directory in vendor/mage2pro/core/Framework/lib/fs/contents.php on line 55»
			 * in the IntellliJ IDEA's console if xDebug is enabled": https://github.com/mage2pro/core/issues/424
			 * 3) I use @uses is_file() to reject directories.
			 */
			!$isURL && (!is_file($f) || !file_exists($f) || !is_readable($f)) ? false : @file_get_contents($f, false, $rs)
		);}
		,true !== $onE ? $onE : function() use ($f, $isURL) {df_error(
			'Unable to read the %s «%s».', $isURL ? 'URL' : 'file', $f
		);}
	);
}