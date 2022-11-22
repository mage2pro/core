<?php

/**
 * 2016-08-26
 * Вставляет новые элементы внутрь массива.
 * https://php.net/manual/function.array-splice.php
 * Если нужно вставить только один элемент, то необязательно обрамлять его в массив.
 * 2016-11-23
 * Достоинство этой функции перед @uses array_splice() ещё и в отсутствии требования передачи первого параметра по ссылке.
 * 2016-11-24 Отныне функция правильно работает с ассоциативными массивами.
 * @used-by \Df\Sso\Source\Button\Type\UNL::map()
 * @used-by \Dfe\SecurePay\Signer::sign()
 * @param mixed|mixed[] $add
 */
function dfa_insert(array $a, int $pos, $add):array {
	if (!is_array($add) || array_is_list($add)) {
		array_splice($a, $pos, 0, $add);
	}
	else {
		# 2016-11-24 Отныне функция правильно работает с ассоциативными массивами: http://stackoverflow.com/a/1783125
		$a = array_slice($a, 0, $pos, true) + $add + array_slice($a, $pos, null, true);
	}
	return $a;
}

/**
 * 2015-02-07
 * Функция предназначена для работы только с ассоциативными массивами!
 * Фантастически лаконичное и красивое решение!
 * Вынес его в отдельную функцию, чтобы не забыть!
 * Например:
 *		$source = ['RU' => 'Россия', 'KZ' => 'Казахстан', 'TJ' => 'Таджикистан', 'US' => 'США', 'CA' => 'Канада'];
 *		$priorityKeys = ['TJ', 'CA'];
 *		print_r(dfa_prepend_by_keys($source, $priorityKeys));
 * Вернёт:
 *	 [
 *		 [TJ] => Таджикистан
 *		 [CA] => Канада
 *		 [RU] => Россия
 *		 [KZ] => Казахстан
 *		 [US] => США
 *	 ]
 * http://3v4l.org/QYffO
 * Обратите внимание, что @uses array_flip() корректно работает с пустыми массивами:
 *	print_r(array_flip([]));
 * вернёт array
 * http://3v4l.org/Kd01X
 * @used-by dfa_prepend_by_values()
 * @param array(string => mixed) $a
 * @param string[] $k
 * @return array(string => mixed)
 */
function dfa_prepend_by_keys(array $a, array $k):array {return dfa($a, $k) + $a;}

/**
 * 2015-02-07
 * Функция предназначена для работы только с ассоциативными массивами!
 * Фантастически лаконичное и красивое решение!
 * Вынес его в отдельную функцию, чтобы не забыть!
 * Например:
 *		$source = [
 *			'Россия' => 'RU'
 *			,'Казахстан' => 'KZ'
 *			,'Таджикистан' => 'TJ'
 *			,'США' => 'US'
 *			,'Канада' => 'CA'
 *		];
 *		$priorityValues = ['TJ', 'CA'];
 *		print_r(dfa_prepend_by_values($source, $priorityValues));
 * вернёт:
 *		[
 *			[Таджикистан] => TJ
 *			[Канада] => CA
 *			[Россия] => RU
 *			[Казахстан] => KZ
 *			[США] => US
 *		]
 * http://3v4l.org/tNms4
 * 2020-01-29 @deprecated It is unused.
 * @uses dfa_prepend_by_keys()
 * @param array(string => mixed) $a
 * @param string[] $v
 * @return array(string => mixed)
 */
function dfa_prepend_by_values(array $a, array $v):array {return array_flip(dfa_prepend_by_keys(array_flip($a), $v));}


