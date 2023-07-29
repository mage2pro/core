<?php
use Df\Core\Exception as DFE;

/**
 * Оба входных массива должны быть ассоциативными
 * 2022-10-31 @deprecated It is unused.
 * @param array(string => mixed) $a1
 * @param array(string => mixed) $a2
 * @return array(string => mixed)
 */
function df_merge_not_empty(array $a1, array $a2):array {return array_filter($a2) + $a1;}

/**
 * 2017-10-28
 * Plain `array_merge($r, $b)` works wronly, if $b contains contains SOME numeric-string keys like "99":
 * https://github.com/mage2pro/core/issues/40#issuecomment-340139933
 * https://stackoverflow.com/a/5929671
 * @used-by dfa_select_ordered()
 * @param array(string|int => mixed) $r
 * @param array(string|int => mixed) $b
 * @return array(string|int => mixed)
 */
function dfa_merge_numeric(array $r, array $b):array {
	foreach ($b as $k => $v) {
		$r[$k] = $v;
	}
	return $r;
}

/**
 * 2015-02-18
 * 1) По смыслу функция @see dfa_merge_r() аналогична методу @see \Magento\Framework\Simplexml\Element::extend()
 * и предназначена для слияния настроечных опций,
 * только, в отличие от @see \Magento\Framework\Simplexml\Element::extend(),
 * @see dfa_merge_r() сливает не XML, а ассоциативные массивы.
 * 3) Вместо @see dfa_merge_r() нельзя использовать ни
 * @see array_replace_recursive(), ни @see array_merge_recursive(),
 * ни тем более @see array_replace() и @see array_merge()
 * 3.1) Нерекурсивные аналоги отметаются сразу, потому что не способны сливать вложенные структуры.
 * 3.2) Но и стандартные рекурсивные функции тоже не подходят:
 * 3.2.1) array_merge_recursive(['width' => 180], ['width' => 200]) вернёт: ['width' => [180, 200]]
 * https://php.net/manual/function.array-merge-recursive.php
 * 3.2.2) Наша функция dfa_merge_r(['width' => 180], ['width' => 200]) вернёт ['width' => 200]
 * 3.2.3) array_replace_recursive(['x' => ['A', 'B']], ['x' => 'C']) вернёт: ['x' => ['С', 'B']]
 * https://php.net/manual/function.array-replace-recursive.php
 * 3.2.4) Наша функция dfa_merge_r(['x' => ['A', 'B']], ['x' => 'C']) вернёт ['x' => 'C']
 * 2018-11-13
 * 1) dfa_merge_r(
 *		['TBCBank' => ['1111' => ['a' => 'b']]]
 *		,['TBCBank' => ['2222' => ['c' => 'd']]]
 * )
 * is: 'TBCBank' => ['1111' => ['a' => 'b'], '2222' => ['c' => 'd']]
 * 2) dfa_merge_r(
 *		['TBCBank' => [1111 => ['a' => 'b']]]
 *		,['TBCBank' => [2222 => ['c' => 'd']]]
 * )
 * is: 'TBCBank' => [1111 => ['a' => 'b'], 2222 => ['c' => 'd']]
 * @used-by df_ci_add()
 * @used-by dfa_merge_r()
 * @used-by df_log()
 * @used-by df_log_l()
 * @used-by df_oi_add()
 * @used-by df_sentry()
 * @used-by \Dfe\AlphaCommerceHub\W\Reader::reqFilter()
 * @used-by \Dfe\TBCBank\Test\CaseT\Regular::t02()
 * @param array(string => mixed) $old
 * @param array(string => mixed) $new
 * @return array(string => mixed)
 * @throws DFE
 */
function dfa_merge_r(array $old, array $new):array {
	# Здесь ошибочно было бы $r = [], потому что если ключ отсутствует в $new, то тогда он не попадёт в $r.
	$r = $old; /** @var array(string => mixed) $r */
	foreach ($new as $k => $newV) {/** @var int|string $k */ /** @var mixed $newV */
		$oldV = dfa($old, $k); /** @var mixed $oldV */
		if (!is_array($oldV)) {
			# 2016-08-23 unset добавил сегодня.
			if (is_null($newV)) {
				unset($r[$k]);
			}
			else {
				$r[$k] = $newV;
			}
		}
		elseif (is_array($newV)) {
			$r[$k] = dfa_merge_r($oldV, $newV);
		}
		elseif (is_null($newV)) {
			unset($r[$k]);
		}
		else {
			# Если значение по умолчанию является массивом, а новое значение не является массивом,
			# то это наверняка говорит об ошибке программиста.
			df_error(
				"dfa_merge_r: the default value of key «{$k}» is an array {defaultValue},"
				. "\nbut the programmer mistakenly tries to substitute it"
				. ' with the value {newValue} of type «{newType}».'
				. "\nThe new value should be an array or `null`."
				,[
					'{defaultValue}' => df_t()->singleLine(df_dump($oldV))
					,'{newType}' => gettype($newV)
					,'{newV}' => df_dump($newV)
				]
			);
		}
	}
	return $r;
}