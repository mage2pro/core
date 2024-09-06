<?php
/**
 * 2016-01-14
 * 2019-06-05 Parent functions with multiple different arguments are not supported!
 * 2022-11-23 `callable` as an argument type is supported by PHP ≥ 5.4:
 * https://github.com/mage2pro/core/issues/174#user-content-callable
 * 2024-06-10
 * "`df_call_a()` should accept the first 2 arguments in an arbitrary ordering": https://github.com/mage2pro/core/issues/417
 * @used-by df_body_class()
 * @used-by df_camel_to_underscore()
 * @used-by df_e()
 * @used-by df_explode_camel()
 * @used-by df_html_b()
 * @used-by df_lcfirst()
 * @used-by df_link_inline()
 * @used-by df_mvar_name()
 * @used-by df_strtolower()
 * @used-by df_strtoupper()
 * @used-by df_tab()
 * @used-by df_trim_ds()
 * @used-by df_ucfirst()
 * @used-by df_ucwords()
 * @used-by df_underscore_to_camel()
 * @param callable|iterable $a1
 * @param callable|iterable $a2
 * @param mixed|mixed[] $pAppend [optional]
 * @param mixed|mixed[] $pPrepend [optional]
 * @return mixed|mixed[]
 */
function df_call_a($a1, $a2, $pAppend = [], $pPrepend = [], int $keyPosition = 0) {
	[$a, $f] = dfaf($a1, $a2); /** @var iterable $a */ /** @var callable $f */
	/**
	 * 2016-11-13 We can not use @see df_args() here
	 * 2019-06-05
	 * The parent function could be called in 3 ways:
	 * 		1) With a single array argument.
	 * 		2) With a single scalar (non-array) argument.
	 * 		3) With multiple arguments.
	 * `1 === count($a)` in the 1st and 2nd cases.
	 *  1 <> count($a) in the 3rd case.
	 * We should return an array in the 1st and 3rd cases, and a scalar result in the 2nd case.
	 */
	if (1 === count($a)) {
		$a = $a[0]; # 2019-06-05 It is the 1st or the 2nd case: a single argument (a scalar or an array).
	}
	return
		!is_array($a) # 2019-06-05 It is the 2nd case: a single scalar (non-array) argument
		? call_user_func_array($f, array_merge($pPrepend, [$a], $pAppend))
		: df_map($f, $a, $pAppend, $pPrepend, $keyPosition
	);
}