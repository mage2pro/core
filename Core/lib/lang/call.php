<?php
use Closure as F;

/**
 * 2015-12-30
 * Унифицирует вызов калбэков:
 * позволяет в качестве $method передавать как строковое название метода,
 * так и анонимную функцию, которая в качестве аргумента получит $object.
 * https://3v4l.org/pPGtA
 * 2017-07-09 Now the function can accept an array as $object.
 * @used-by df_column()
 * @used-by df_each()
 * @param object|mixed|array $o
 * @param string|callable|F $m
 * @return mixed
 */
function df_call($o, $m, array $p = []) {/** @var mixed $r */
	if (df_is_assoc($o)) {
		$r = dfa($o, $m);
	}
	elseif (!is_string($m)) {# $m — инлайновая функция
		$r = call_user_func_array($m, array_merge([$o], $p));
	}
	else {
		$functionExists = function_exists($m); /** @var bool $functionExists */
		/**
		 * 2020-02-05
		 * 1) @uses is_callable() always returns `true` for an object which the magic `__call` method
		 * (e.g., for all @see \Magento\Framework\DataObject ancestors),
		 * but it returns `false` for private and protected (so non-callable) methods.
		 * 2) @uses method_exists() returns `true` even for private and protected (so non-callable) method,
		 * but it returns `false` for absent methods handled by `__call`.
		 * 3) The conjunction of these 2 checks returns `true` only for publicly accessible and really exists
		 * (not handled by `__call`) methods.
		 */
		$methodExists = is_callable([$o, $m]) && method_exists($o, $m); /** @var bool $methodExists */
		$callable = null; /** @var ?callable $callable */
		if ($functionExists && !$methodExists) {
			$callable = $m;
			$p = array_merge([$o], $p);
		}
		elseif ($methodExists && !$functionExists) {
			$callable = [$o, $m];
		}
		if ($callable) {
			$r = call_user_func_array($callable, $p);			
		}
		elseif (df_has_gd($o)) {
			$r = dfad($o, $m);			
		}
		elseif (!$functionExists) {
			df_error("Unable to call «{$m}».");
		}
		else {
			df_error("An ambiguous name: «{$m}».");
		}
	}
	return $r;
}

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

/**
 * 2016-02-09
 * https://3v4l.org/iUQGl
 *	 function a($b) {return is_callable($b);}
 *	 a(function() {return 0;}); возвращает true
 * https://3v4l.org/MfmCj
 *	is_callable('intval') возвращает true
 * @used-by df_const()
 * @used-by df_if()
 * @used-by df_if1()
 * @used-by df_if2()
 * @used-by df_leaf()
 * @used-by dfa()
 * @param mixed|callable $v
 * @param mixed ...$a [optional]
 * @return mixed
 */
function df_call_if($v, ...$a) {return is_callable($v) && !is_string($v) && !is_array($v)
	? call_user_func_array($v, $a) : $v
;}