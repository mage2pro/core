<?php
/**
 * 2016-01-14 Эта функция умеет работать с UTF-8, в отличие от стандартной функции @see lcfirst()
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax rejects arrays: https://3v4l.org/jFdPm
 * @see df_ucfirst()
 * @used-by df_camel_to_underscore()
 * @used-by df_class_second_lc() 
 * @used-by df_explode_class_lc() 
 * @used-by df_explode_class_lc_camel()
 * @param string|string[] ...$a
 * @return string|string[]
 */
function df_lcfirst(...$a) {return df_call_a(function(string $s):string {return
	mb_strtolower(mb_substr($s, 0, 1)) . mb_substr($s, 1)
;}, $a);}

/**
 * 2016-05-22
 * 2022-10-31 @deprecated It is unused.
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax rejects arrays: https://3v4l.org/jFdPm
 * @param string|string[] ...$a
 * @return string|string[]
 */
function df_strtolower(...$a) {return df_call_a(function(string $s):string {return mb_strtolower($s);}, $a);}

/**
 * 2016-05-19
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax rejects arrays: https://3v4l.org/jFdPm
 * @see df_lcfirst
 * @used-by \Dfe\Stripe\Block\Multishipping::cardholder()
 * @param string|string[] ...$a
 * @return string|string[]
 */
function df_strtoupper(...$a) {return df_call_a(function(string $s):string {return mb_strtoupper($s);}, $a);}

/**
 * Эта функция умеет работать с UTF-8, в отличие от стандартной функции @see ucfirst()
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax rejects arrays: https://3v4l.org/jFdPm
 * @see df_lcfirst
 * @used-by df_assert_gd()
 * @used-by df_cc_class_uc()
 * @used-by df_underscore_to_camel()
 * @used-by \Df\Config\Source\LetterCase::apply()
 * @used-by \Df\Qa\Trace\Frame::url()
 * @used-by \Dfe\TwoCheckout\LineItem::build()
 * @param string|string[] ...$a
 * @return string|string[]
 */
function df_ucfirst(...$a) {return df_call_a(function(string $s):string {return
	mb_strtoupper(mb_substr($s, 0, 1)) . mb_substr($s, 1)
;}, $a);}

/**
 * Эта функция умеет работать с UTF-8, в отличие от стандартной функции @see ucwords()
 * https://php.net/manual/function.mb-convert-case.php
 * https://php.net/manual/function.mb-convert-case.php#refsect1-function.mb-convert-case-parameters
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax rejects arrays: https://3v4l.org/jFdPm
 * @see df_ucfirst
 * @used-by \Df\Config\Source\LetterCase::apply()
 * @param string|string[] ...$a
 * @return string|string[]
 */
function df_ucwords(...$a) {return df_call_a(function(string $s):string {return mb_convert_case(
	$s, MB_CASE_TITLE, 'UTF-8'
);}, $a);}