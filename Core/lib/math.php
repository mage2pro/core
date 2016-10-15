<?php
/**
 * @param float|int $value
 * @return int
 */
function df_ceil($value) {return (int)ceil($value);}

/**
 * @param float|int $value
 * @return int
 */
function df_floor($value) {return (int)floor($value);}

/**
 * @param float|int $value
 * @return int
 */
function df_round($value) {return (int)round($value);}

/**
 * 2015-02-26
 * Складывает 2 числовых массива как векторы.
 * Второй аргумент может быть также числом:
 * тогда считается, что все координаты этого вектора равны данному числу.
 * @param int[]|float[] $a
 * @param int|float|int[]|float[] $b
 * @return int[]|float[]
 */
function df_vector_sum(array $a, $b) {
	/** @var int $length */
	$length = count($a);
	if (!is_array($b)) {
		$b = dfa_fill(0, $length, $b);
	}
	else {
		df_assert($length === count($b));
		$b = array_values($b);
	}
	$a = array_values($a);
	/** @var int[]|float[] $result */
	$result = [];
	for ($i = 0; $i < $length; $i++) {
		$result[]= $a[$i] + $b[$i];
	}
	return $result;
}




