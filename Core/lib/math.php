<?php
/**
 * 2022-10-30
 * @uses round() returns a float, not an int: https://www.php.net/manual/function.round.php
 * @used-by df_date_from_timestamp_14()
 * @used-by df_num_days()
 * @param float|int $v
 */
function df_round($v):int {return (int)round($v);}

/**
 * 2015-02-26
 * Складывает 2 числовых массива как векторы.
 * Второй аргумент может быть также числом: тогда считается, что все координаты этого вектора равны данному числу.
 * 2022-10-30 @deprecated It is unused.
 * @param int[]|float[] $a
 * @param int|float|int[]|float[] $b
 * @return int[]|float[]
 */
function df_vector_sum(array $a, $b):array {
	$length = count($a); /** @var int $length */
	if (!is_array($b)) {
		$b = dfa_fill(0, $length, $b);
	}
	else {
		df_assert_eq($length, count($b));
		$b = array_values($b);
	}
	$a = array_values($a);
	$r = []; /** @var int[]|float[] $r */
	for ($i = 0; $i < $length; $i++) {
		$r[]= $a[$i] + $b[$i];
	}
	return $r;
}