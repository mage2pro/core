<?php
/**
 * 2018-04-24
 * 2024-04-08 I added the `is_null($k)` case: https://github.com/thehcginstitute-com/m1/issues/551
 * @used-by Doormall\Shipping\Partner\Entity::locations()
 * @param array(int|string => mixed) $a
 * @param string|int|null $k
 * @return array(int|string => array(int|string => mixed))
 */
function dfa_group(array $a, $k = null):array {
	$r = []; /** @var array(int|string => array(int|string => mixed)) $r */
	if (is_null($k)) {
		foreach ($a as $k => $index) { /** @var int|string $index */
			if (!isset($r[$index])) {
				$r[$index] = [];
			}
			$r[$index][] = $k;
		}
	}
	else {
		$isInt = is_int($k); /** @var bool $isInt */
		foreach ($a as $v) { /** @var mixed $v */
			$index = $v[$k]; /** @var string $index */
			if (!isset($r[$index])) {
				$r[$index] = [];
			}
			unset($v[$k]);
			$r[$index][] = 1 === count($v) ? df_first($v) : (!$isInt ? $v : array_values($v));
		}
	}
	return $r;
}