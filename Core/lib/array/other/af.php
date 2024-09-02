<?php
/**
 * 2017-02-18 [iterable|callable, iterable|callable] => [iterable, callable]
 * @used-by df_call_a()
 * @used-by df_filter_f()
 * @used-by df_find()
 * @used-by df_map()
 * @used-by dfak_transform()
 * @param iterable|callable $a
 * @param iterable|callable $b
 * @return array(iterable|callable)
 */
function dfaf($a, $b):array {
	# 2020-02-15
	# "A variable is expected to be a traversable or an array, but actually it is a «object»":
	# https://github.com/tradefurniturecompany/site/issues/36
	# 2024-05-08
	# 1) https://php.watch/versions/8.2/partially-supported-callable-deprecation
	# 2.1) `is_callable([__CLASS__, 'f'])` for a private `f` is allowed: https://3v4l.org/ctZJG
	# 2.2) `array_map([__CLASS__, 'f'], [1, 2, 3])` for a private `f` is allowed too: https://3v4l.org/29Zim
	# 2024-06-11 "Improve `dfaf()`": https://github.com/mage2pro/core/issues/421
	$assert = function(bool $cond, string $m) use($a, $b):void {df_assert($cond, "dfaf(): $m.", ['a' => $a, 'b' => $b]);};
	/** @var bool $ca */ /** @var bool $cb *//** @var bool $ia */ /** @var bool $ib */
	[$ca, $cb, $ia, $ib] = [is_callable($a), is_callable($b), is_iterable($a), is_iterable($b)];
	if ($ca && $cb) {
		$assert($ia xor $ib, !$ia ? 'none of arguments are `iterable`': 'both arguments are `callable` and `iterable`');
		$r = $ia ? [$a, $b] : [$b, $a];
	}
	else {
		$assert($ca || $cb, 'none of arguments are callable');
		if ($ca) {
			$assert($ib, '$a is `callable`, and $b is not `callable`, so $b must be `iterable`');
			$r = [$b, $a];
		}
		else {
			$assert($ia, '$b is `callable`, and $a is not `callable`, so $a must be `iterable`');
			$r = [$a, $b];
		}
	}
	return $r;
}