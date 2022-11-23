<?php
/**
 * 2016-01-14 Эта функция умеет работать с UTF-8, в отличие от стандартной функции @see lcfirst()
 * @see df_ucfirst()
 * @used-by df_camel_to_underscore()
 * @used-by df_class_second_lc() 
 * @used-by df_explode_class_lc() 
 * @used-by df_explode_class_lc_camel()
 * @param string|string[] ...$args
 * @return string|string[]
 */
function df_lcfirst(...$args) {return df_call_a(function($s) {return
	mb_strtolower(mb_substr($s, 0, 1)) . mb_substr($s, 1)
;}, $args);}

/**
 * 2016-05-22
 * 2022-10-31 @deprecated It is unused.
 * @param string|string[] ...$args
 * @return string|string[]
 */
function df_strtolower(...$args) {return df_call_a(function($s) {return mb_strtolower($s);}, $args);}

/**
 * 2016-05-19
 * @see df_lcfirst
 * @used-by \Dfe\Stripe\Block\Multishipping::cardholder()
 * @param string|string[] ...$args
 * @return string|string[]
 */
function df_strtoupper(...$args) {return df_call_a(function($s) {return mb_strtoupper($s);}, $args);}

/**
 * Эта функция умеет работать с UTF-8, в отличие от стандартной функции @see ucfirst()
 * @see df_lcfirst
 * @used-by df_assert_gd()
 * @used-by df_cc_class_uc()
 * @used-by df_underscore_to_camel()
 * @used-by \Dfe\TwoCheckout\LineItem::build()
 * @used-by \Df\Config\Source\LetterCase::apply()
 * @param string|string[] ...$args
 * @return string|string[]
 */
function df_ucfirst(...$args) {return df_call_a(function($s) {return
	mb_strtoupper(mb_substr($s, 0, 1)) . mb_substr($s, 1)
;}, $args);}

/**
 * Эта функция умеет работать с UTF-8, в отличие от стандартной функции @see ucwords()
 * https://php.net/manual/function.mb-convert-case.php
 * https://php.net/manual/function.mb-convert-case.php#refsect1-function.mb-convert-case-parameters
 * @see df_ucfirst
 * @used-by \Df\Config\Source\LetterCase::apply()
 * @param string|string[] ...$args
 * @return string|string[]
 */
function df_ucwords(...$args) {return df_call_a(function($s) {return mb_convert_case($s, MB_CASE_TITLE, 'UTF-8');}, $args);}