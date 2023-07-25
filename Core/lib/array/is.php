<?php
/** 2022-10-17 @see array_is_list() has been added to PHP 8.1: https://www.php.net/manual/function.array-is-list.php **/
if (!function_exists('array_is_list')) {
	/**
	 * 2015-02-07
	 * Обратите внимание, что алгоритмов проверки массива на ассоциативность найдено очень много:
	 * http://stackoverflow.com/questions/173400/how-to-check-if-php-array-is-associative-or-sequential
	 * Я уже давно (несколько лет) использую приведённый ниже.
	 * Пока он меня устраивает, да и сама задача такой проверки возникает у меня в Российской сборке Magento редко,
	 * и не замечал её особого влияния на производительность системы.
	 * Возможно, другие алгоритмы лучше, лень разбираться.
	 * 2017-10-29 It returns `true` for an empty array.
	 * @used-by df_is_assoc()
	 * @used-by df_ksort_r_ci()
	 * @used-by df_sort()
	 * @used-by dfa_deep_slice()
	 * @used-by dfa_insert()
	 * @used-by dfak_transform()
	 * @used-by \Df\Payment\ConfigProvider::configOptions()
	 * @used-by \Df\Payment\Method::isAvailable()
	 * @param array(int|string => mixed) $a
	 */
	function array_is_list(array $a):bool {
		$r = true; /** @var bool $r */
		foreach (array_keys($a) as $k => $v) {
			# 2015-02-07
			# Согласно спецификации PHP, ключами массива могут быть целые числа, либо строки.
			# Третьего не дано.
			# https://php.net/manual/language.types.array.php
			# 2017-02-18
			# На самом деле ключом может быть и null, что неявно приводится к пустой строке:
			# http://stackoverflow.com/a/18247435
			# 2015-02-07
			# Раньше тут стояло !is_int($key)
			# Способ проверки $key !== $value нашёл по ссылке ниже:
			# http://www.php.net/manual/en/function.is-array.php#84488
			if ($k !== $v) {
				$r = false;
				break;
			}
		}
		return $r;
	}
}

/**
 * 2015-02-07
 * 2017-10-29 It returns `true` for an empty array.
 * 2022-10-17 @uses array_is_list() has been added to PHP 8.1: https://www.php.net/manual/function.array-is-list.php
 * @used-by df_assert_assoc()
 * @used-by df_call()
 * @used-by df_clean()
 * @used-by df_ksort()
 * @used-by \Df\Xml\X::importArray()
 * @param array(int|string => mixed) $a
 */
function df_is_assoc(array $a):bool {return !$a || !array_is_list($a);}

/**
 * 2023-07-25
 * @uses is_object()
 * @used-by \Df\Qa\Dumper::dumpArray()
 * @param iterable $a
 */
function dfa_has_objects($a):bool {return !!df_find($a, 'is_object', [], [], true);}