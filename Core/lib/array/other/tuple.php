<?php
/**
 * http://en.wikipedia.org/wiki/Tuple
 * 2020-02-12
 * 1) df_tuple(['a' => [1, 2, 3], 'b' => [4, 5]]) → [['a' => 1, 'b' => 4], ['a' => 2, 'b' => 5], ['a' => 3, 'b' => null]]
 * 2) df_tuple([[1, 2, 3], [4, 5]]) → [[1, 4], [2, 5], [3, null]]
 * 2022-10-31 @deprecated It is unused.
 */
function df_tuple(array $arrays):array {
	$r = []; /** @var array $r */
	$countItems = max(array_map('count', $arrays)); /** @var int $count */
	for ($ordering = 0; $ordering < $countItems; $ordering++) {
		$item = []; /** @var array $item */
		foreach ($arrays as $arrayName => $array) {
			$item[$arrayName]= dfa($array, $ordering);
		}
		$r[$ordering] = $item;
	}
	return $r;
}
