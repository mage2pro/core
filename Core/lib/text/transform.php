<?php
/**
 * @param string $text
 * @return string
 */
function df_camelize($text) {return implode(df_ucfirst(df_explode_class(df_trim($text))));}

/**
 * 2016-01-14
 * @see df_ucfirst()
 * Эта функция умеет работать с UTF-8, в отличие от стандартной функции @see lcfirst()
 * @param string[] ...$args
 * @return string|string[]
 */
function df_lcfirst(...$args) {return df_call_a(function($s) {
	return mb_strtolower(mb_substr($s, 0, 1)) . mb_substr($s, 1);
}, $args);}

/**
 * 2016-05-22
 * @param string[] ...$args
 * @return string|string[]
 */
function df_strtolower(...$args) {return df_call_a(function($s) {return mb_strtolower($s);}, $args);}

/**
 * 2016-05-19
 * @see df_lcfirst
 * @used-by \Dfe\Stripe\Block\Multishipping::cardholder()
 * @param string[] ...$args
 * @return string|string[]
 */
function df_strtoupper(...$args) {return df_call_a(function($s) {
	return mb_strtoupper($s);
}, $args);}

/**
 * Эта функция умеет работать с UTF-8, в отличие от стандартной функции @see ucfirst()
 * @see df_lcfirst
 * @param string[] ...$args
 * @return string|string[]
 */
function df_ucfirst(...$args) {return df_call_a(function($s) {
	return mb_strtoupper(mb_substr($s, 0, 1)) . mb_substr($s, 1);
}, $args);}

/**
 * Эта функция умеет работать с UTF-8, в отличие от стандартной функции @see ucwords()
 * http://php.net/manual/function.mb-convert-case.php
 * http://php.net/manual/function.mb-convert-case.php#refsect1-function.mb-convert-case-parameters
 * @see df_ucfirst
 * @param string[] ...$args
 * @return string|string[]
 */
function df_ucwords(...$args) {return df_call_a(function($s) {
	return mb_convert_case($s, MB_CASE_TITLE, 'UTF-8');
}, $args);}

/**
 * 2016-08-10
 * REFUND_ISSUED => RefundIssued
 * refund_issuED => RefundIssued
 * @param string[] ...$args
 * @return string|string[]
 */
function df_underscore_to_camel(...$args) {return df_call_a(function($s) {
	return implode(df_ucfirst(explode('_', mb_strtolower($s))));
}, $args);}