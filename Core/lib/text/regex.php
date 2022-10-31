<?php
use Df\Core\Text\Regex as R;

/**
 * @used-by \Df\Typography\Font::variantNumber()
 * @param string $pattern
 * @param string $subject
 * @param bool $throwOnNotMatch [optional]
 * @return int|null|bool
 */
function df_preg_int($pattern, $subject, $throwOnNotMatch = false) {return R::i(
	$pattern, $subject, $throwOnError = true, $throwOnNotMatch
)->matchInt();}

/**
 * 2015-03-23 Добавил поддержку нескольких пар круглых скобок (в этом случае функция возвращает массив).
 * @used-by df_preg_prefix()
 * @used-by df_xml_parse_header()
 * @param string $pattern
 * @param string $subject
 * @param bool $throwOnNotMatch [optional]
 * @return string|string[]|null|bool
 */
function df_preg_match($pattern, $subject, $throwOnNotMatch = false) {return R::i(
	$pattern, $subject, $throwOnError = true, $throwOnNotMatch
)->match();}

/**
 * 2018-11-11
 * @used-by \Dfe\TBCBank\Test\CaseT\Validator::t01()
 * @param string $prefix
 * @param string $subject
 * @param bool $throwOnNotMatch [optional]
 * @return int|null|bool
 */
function df_preg_prefix($prefix, $subject, $throwOnNotMatch = false) {return df_preg_match(
	sprintf('#^%s([\S\s]*)#', preg_quote($prefix)), $subject, $throwOnNotMatch
);}

/**
 * 2022-10-31 @deprecated It is unused.
 * @param string $pattern
 * @param string $subject
 * @param bool $throwOnError [optional]
 * @return bool
 * @throws Exception
 */
function df_preg_test($pattern, $subject, $throwOnError = true):bool {return R::i(
	$pattern, $subject, $throwOnError, false
)->test();}