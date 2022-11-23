<?php
/**
 * 2015-02-07
 * Аналог @see array_change_key_case() с поддержкой UTF-8.
 * Реализацию взял отсюда: https://php.net/manual/function.array-change-key-case.php#107715
 * Обратите внимание, что @see array_change_key_case() некорректно работает с UTF-8.
 * Например:
 *		$countries = array('Россия' => 'RU', 'Украина' => 'UA', 'Казахстан' => 'KZ');
 *	array_change_key_case($countries, CASE_UPPER)
 * вернёт:
 *	(
 *		[РнссШя] => RU
 *		[УЪраШна] => UA
 *		[Њазахстан] => KZ
 *	)
 *
 * 2017-02-01
 * Отныне стал использовать константы MB_CASE_LOWER и MB_CASE_UPPER вместо CASE_LOWER и CASE_UPPER.
 * Обратите внимание, что они имеют противоположные значения:
 * CASE_LOWER = 0, а MB_CASE_LOWER = 1
 * CASE_UPPER = 1, а MB_CASE_UPPER = 0.
 *
 * @used-by dfa_key_uc()
 * @param array(string => mixed) $a
 * @param int $c
 * @return array(string => mixed)
 */
function dfa_key_case(array $a, $c):array {return dfak_transform_r($a, function($k) use($c) {return
	mb_convert_case($k, $c, 'UTF-8')
;});}

/**
 * 2017-09-03
 * @used-by \Dfe\Qiwi\API\Validator::codes()
 * @uses df_int()
 * @see df_int_simple()
 * @param array(int|string => mixed) $a
 * @return array(int => mixed)
 */
function dfa_key_int(array $a):array {return dfak_transform($a, 'df_int');}

/**
 * 2020-01-29
 * 2020-02-04
 * It does not change keys of a non-associative array,
 * but it is applied recursively to nested arrays, so it could change keys their keys.
 * @used-by \Dfe\Sift\API\Client::_construct()
 * @param array(string => mixed) $a
 * @param string $p
 * @param bool $req [optional]
 * @return array(string => mixed)
 */
function dfak_prefix(array $a, $p, $req = false):array {return dfak_transform($a, function($k) use($p) {return
	"$p$k"
;}, $req);}

/**
 * 2017-02-01
 * 2020-01-29
 * 2020-02-04
 * It does not change keys of a non-associative array,
 * but it is applied recursively to nested arrays, so it could change keys their keys.
 * @used-by df_headers()
 * @used-by dfa_key_int()
 * @used-by dfak_prefix()
 * @used-by dfak_transform()
 * @used-by dfak_transform_r()
 * @used-by \Df\Framework\Request::extra()
 * @used-by \Df\Sentry\Client::tags()
 * @used-by \Df\Sentry\Extra::adjust()
 * @used-by \Dfe\YandexKassa\Charge::pLoan()
 * @param array|callable|Traversable $a1
 * @param array|callable|Traversable $a2
 * @param bool $req [optional]
 * @return array(string => mixed)
 */
function dfak_transform($a1, $a2, $req = false):array {
	# 2020-03-02, 2022-10-31
	# 1) Symmetric array destructuring requires PHP ≥ 7.1:
	#		[$a, $b] = [1, 2];
	# https://github.com/mage2pro/core/issues/96#issuecomment-593392100
	# We should support PHP 7.0.
	# https://3v4l.org/3O92j
	# https://www.php.net/manual/migration71.new-features.php#migration71.new-features.symmetric-array-destructuring
	# https://stackoverflow.com/a/28233499
	list($a, $f) = dfaf($a1, $a2); /** @var array|Traversable $a */ /** @var callable $f */
	$a = df_ita($a);
	$l = array_is_list($a); /** @var bool $l */
	return df_map_kr($a, function($k, $v) use($f, $req, $l) {return [
		$l ? $k : $f($k), !$req || !is_array($v) ? $v : dfak_transform($v, $f, $req)
	];});
}

/**
 * 2020-01-30 It works recursively.
 * 2020-02-04
 * It does not change keys of a non-associative array,
 * but it is applied recursively to nested arrays, so it could change keys their keys.
 * @used-by dfa_key_case()
 * @used-by dfak_prefix()
 * @used-by dfak_transform()
 * @param array|callable|Traversable $a1
 * @param array|callable|Traversable $a2
 * @return array(string => mixed)
 */
function dfak_transform_r($a1, $a2):array {return dfak_transform($a1, $a2, true);}

/**
 * 2017-02-01
 * @used-by \Dfe\PostFinance\Signer::sign()
 * @param array(string => mixed) $a
 * @return array(string => mixed)
 */
function dfa_key_uc(array $a):array {return dfa_key_case($a, MB_CASE_UPPER);}