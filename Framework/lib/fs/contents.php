<?php
use Closure as F;
/**
 * 2022-11-24
 * @used-by df_test_file_l()
 * @param string $f
 * @param F|bool|mixed $onE [optional]
 * @param ?resource $rs [optional]
 */
function df_contents(string $f, $onE = true, $rs = null):string {return df_try(
	function() use ($f, $rs):string {return df_assert_ne(false, @file_get_contents($f, null, $rs));}
	,true !== $onE ? $onE : function() use ($f, $rs) {df_error('Unable to read the %s «%s».', $rs ? 'URL' : 'file', $f);}
);}