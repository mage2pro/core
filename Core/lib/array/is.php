<?php
/**
 * 2015-02-07
 * Обратите внимание, что алгоритмов проверки массива на ассоциативность найдено очень много:
 * http://stackoverflow.com/questions/173400/how-to-check-if-php-array-is-associative-or-sequential
 * Я уже давно (несоколько лет) использую приведённый ниже.
 * Пока он меня устраивает, да и сама задача такой проверки
 * возникает у меня в Российской сборке Magento редко
 * и не замечал её особого влияния на производительность системы.
 * Возможно, другие алгоритмы лучше, лень разбираться.
 * 2017-10-29 It returns `true` for an empty array.
 * @used-by df_assert_assoc()
 * @used-by df_call()
 * @used-by df_clean()
 * @used-by df_json_sort()
 * @used-by df_ksort_r_ci()
 * @used-by df_sort()
 * @used-by dfa_insert()
 * @used-by \Df\API\Facade::p()
 * @used-by \Df\Payment\ConfigProvider::configOptions()
 * @used-by \Df\Payment\Method::isAvailable()
 * @used-by \Df\Xml\X::importArray()
 * @param array(int|string => mixed) $a
 * @return bool
 */
function df_is_assoc(array $a) {
	if (!($r = !$a)) { /** @var bool $r */
		foreach (array_keys($a) as $k => $v) {
			// 2015-02-07
			// Согласно спецификации PHP, ключами массива могут быть целые числа, либо строки.
			// Третьего не дано.
			// http://php.net/manual/language.types.array.php
			// 2017-02-18
			// На самом деле ключом может быть и null, что неявно приводится к пустой строке:
			// http://stackoverflow.com/a/18247435
			// 2015-02-07
			// Раньше тут стояло !is_int($key)
			// Способ проверки $key !== $value нашёл по ссылке ниже:
			// http://www.php.net/manual/en/function.is-array.php#84488
			if ($k !== $v) {
				$r = true;
				break;
			}
		}
	}
	return $r;
}

/**
 * 2015-04-17
 * Проверяет, является ли массив многомерным.
 * http://stackoverflow.com/a/145348
 * Пока никем не используется.
 * @param array(int|string => mixed) $a
 * @return bool
 */
function df_is_multi(array $a) {
	$r = false; /** @var bool $r */
	foreach ($a as $v) {
		/** @var mixed $v */
		if (is_array($v)) {
			$r = true;
			break;
		}
	}
	return $r;
}