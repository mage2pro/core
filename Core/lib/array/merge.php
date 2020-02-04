<?php
use Df\Core\Exception as DFE;

/**
 * 2015-02-18
 * По смыслу функция @see df_extend() аналогична методу @see \Magento\Framework\Simplexml\Element::extend()
 * и предназначена для слияния настроечных опций,
 * только, в отличие от @see \Magento\Framework\Simplexml\Element::extend(),
 * @see df_extend() сливает не XML, а ассоциативные массивы.
 *
 * Обратите внимание, что вместо @see df_extend() нельзя использовать ни
 * @see array_replace_recursive(), ни @see array_merge_recursive(),
 * ни тем более @see array_replace() и @see array_merge()
 * Нерекурсивные аналоги отметаются сразу, потому что не способны сливать вложенные структуры.
 * Но и стандартные рекурсивные функции тоже не подходят:
 *
 * 1)
 * array_merge_recursive(array('width' => 180), array('width' => 200))
 * вернёт: array(array('width' => array(180, 200)))
 * http://php.net/manual/function.array-merge-recursive.php
 * Наша функция df_extend(array('width' => 180), array('width' => 200))
 * вернёт array('width' => 200)
 *
 * 2)
 * array_replace_recursive(array('x' => array('A', 'B')), array('x' => 'C'))
 * вернёт: array('x' => array('С', 'B'))
 * http://php.net/manual/function.array-replace-recursive.php
 * Наша функция df_extend(array('x' => array('A', 'B')), array('x' => 'C'))
 * вернёт array('x' => 'C')
 *
 * 2018-11-13
 * 1) df_extend(
 *		['TBCBank' => ['1111' => ['a' => 'b']]]
 *		,['TBCBank' => ['2222' => ['c' => 'd']]]
 * )
 * is: 'TBCBank' => ['1111' => ['a' => 'b'], '2222' => ['c' => 'd']]
 * 2) df_extend(
 *		['TBCBank' => [1111 => ['a' => 'b']]]
 *		,['TBCBank' => [2222 => ['c' => 'd']]]
 * )
 * is: 'TBCBank' => [1111 => ['a' => 'b'], 2222 => ['c' => 'd']]
 *
 * @used-by df_ci_add()
 * @used-by df_oi_add()
 * @used-by \Dfe\AlphaCommerceHub\W\Reader::reqFilter()
 * @param array(string => mixed) $defaults
 * @param array(string => mixed) $newValues
 * @return array(string => mixed)
 * @throws DFE
 */
function df_extend(array $defaults, array $newValues) {/** @var array(string => mixed) $r */
	// Здесь ошибочно было бы $r = [], потому что если ключ отсутствует в $newValues, то тогда он не попадёт в $r.
	$r = $defaults;
	foreach ($newValues as $key => $newValue) {
		/** @var int|string $key */ /** @var mixed $newValue */ /** @var mixed $defaultValue */
		$defaultValue = dfa($defaults, $key);
		if (!is_array($defaultValue)) {
			// 2016-08-23 unset добавил сегодня.
			if (is_null($newValue)) {
				unset($r[$key]);
			}
			else {
				$r[$key] = $newValue;
			}
		}
		else {
			if (is_array($newValue)) {
				$r[$key] = df_extend($defaultValue, $newValue);
			}
			else {
				if (is_null($newValue)) {
					unset($r[$key]);
				}
				else {
					// Если значение по умолчанию является массивом,
					// а новое значение не является массивом,
					// то это наверняка говорит об ошибке программиста.
					df_error(
						"df_extend: значением по умолчанию ключа «{$key}» является массив {defaultValue},"
						. "\nоднако программист ошибочно пытается заместить его"
						. ' значением {newValue} типа «{newType}», что недопустимо:'
						. "\nзамещаемое значение для массива должно быть либо массивом, либо «null»."
						,[
							'{defaultValue}' => df_t()->singleLine(df_dump($defaultValue))
							,'{newType}' => gettype($newValue)
							,'{newValue}' => df_dump($newValue)
						]
					);
				}
			}
		}
	}
	return $r;
}

/**
 * Оба входных массива должны быть ассоциативными
 * @param array(string => mixed) $array1
 * @param array(string => mixed) $array2
 * @return array(string => mixed)
 */
function df_merge_not_empty(array $array1, array $array2) {return array_filter($array2) + $array1;}

/**
 * 2015-02-11
 * Эта функция отличается от @see array_merge() только тем,
 * что все вместо нескольких параметров принимает массив из параметров.
 * Это бывает удобно в функциональном программировании, например:
 * @used-by Df_Dataflow_Model_Registry_MultiCollection::getEntities()
 * @used-by Df_Dellin_Model_Request_Rate::getDates()
 * @param array(array(int|string => mixed)) $arrays
 * @return array(int|string => mixed)
 */
function df_merge_single(array $arrays) {return array_merge(...$arrays); }

/**
 * 2017-10-28
 * Plain `array_merge($r, $b)` works wronly,
 * if $b contains contains SOME numeric-string keys like "99":
 * https://github.com/mage2pro/core/issues/40#issuecomment-340139933
 * https://stackoverflow.com/a/5929671
 * @used-by dfa_select_ordered()
 * @param array(string|int => mixed) $r
 * @param array(string|int => mixed) $b
 * @return array(string|int => mixed)
 */
function dfa_merge_numeric(array $r, array $b) {
	foreach ($b as $k => $v) {
		$r[$k] = $v;
	}
	return $r;
}

