<?php
use Closure as F;
/**
 * 2022-11-24
 * @used-by df_test_file_l()
 * @param string $f
 * @param ?resource $context [optional]
 * @param F|bool|mixed $onE [optional]
 */
function df_contents(string $f, $context = null, $onE = true):string {return df_try(
	function() use ($f, $context):string {return df_assert_ne(false, @file_get_contents($f, null, $context));}, $onE
);}