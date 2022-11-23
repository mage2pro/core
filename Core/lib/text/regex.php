<?php
use Df\Core\Text\Regex as R;

/**
 * @used-by \Df\Typography\Font::variantNumber()
 * @return int|null|bool
 */
function df_preg_int(string $pattern, string $subject, bool $throwOnNotMatch = false) {return R::i(
	$pattern, $subject, true, $throwOnNotMatch
)->matchInt();}

/**
 * 2015-03-23 Добавил поддержку нескольких пар круглых скобок (в этом случае функция возвращает массив).
 * @used-by df_preg_prefix()
 * @used-by df_xml_parse_header()
 * @return string|string[]|null|bool
 */
function df_preg_match(string $pattern, string $subject, bool $throwOnNotMatch = false) {return R::i(
	$pattern, $subject, true, $throwOnNotMatch
)->match();}

/**
 * 2018-11-11
 * @used-by \Dfe\TBCBank\Test\CaseT\Validator::t01()
 * @return int|null|bool
 */
function df_preg_prefix(string $prefix, string $subject, bool $throwOnNotMatch = false) {return df_preg_match(
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