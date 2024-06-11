<?php
use Closure as C;
use Df\Core\Exception as E;

/**
 * 2017-02-18 [array|callable, array|callable] => [array, callable]
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
	$ca = is_callable($a); /** @var bool $ca */
	$cb = is_callable($b); /** @var bool $ca */
	if (!$ca || !$cb) {
		df_assert($ca || $cb);
		$r = $ca ? [df_assert_iterable($b), $a] : [df_assert_iterable($a), $b];
	}
	else {
		$ia = is_iterable($a); /** @var bool $ita */
		$ib = is_iterable($b); /** @var bool $ib */
		$assert(!($ia && $ib), 'both arguments are callable and traversable');
		$assert($ia || $ib, 'none of arguments are traversable');
		$r = $ia ? [$a, $b] : [$b, $a];
	}
	return $r;
}