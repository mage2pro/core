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
 * @param object|mixed|array(string => mixed) $o
 * @param string|callable|F $m
 * @param mixed[] $p [optional]
 * @return mixed
 */
function df_call($o, $m, $p = []) {/** @var mixed $r */
	if (is_array($o) && df_is_assoc($o)) {
		$r = dfa($o, $m);
	}
	elseif (!is_string($m)) {// $m — инлайновая функция
		$r = call_user_func_array($m, array_merge([$o], $p));
	}
	else {
		$functionExists = function_exists($m); /** @var bool $functionExists */
		$methodExists = is_callable([$o, $m]); /** @var bool $methodExists */
		/** @var mixed $callable */
		if ($functionExists && !$methodExists) {
			$callable = $m;
			$p = array_merge([$o], $p);
		}
		elseif ($methodExists && !$functionExists) {
			$callable = [$o, $m];
		}
		elseif (!$functionExists) {
			df_error("Unable to call «{$m}».");
		}
		else {
			df_error("An ambiguous name: «{$m}».");
		}
		$r = call_user_func_array($callable, $p);
	}
	return $r;
}

/**
 * 2016-01-14
 * 2019-06-05 Parent functions with multiple different arguments are not supported!
 * @used-by df_1251_from()
 * @used-by df_1251_to()
 * @used-by df_e()
 * @used-by df_explode_camel()
 * @used-by df_html_b()
 * @used-by df_lcfirst()
 * @used-by df_link_inline()
 * @used-by df_strtolower()
 * @used-by df_strtoupper()
 * @used-by df_tab()
 * @used-by df_ucfirst()
 * @used-by df_ucwords()
 * @used-by df_underscore_to_camel()
 * @used-by df_xml_output_html()
 * @used-by df_xml_output_plain()
 * @param callable $f
 * @param mixed[]|mixed[][] $parentArgs
 * @param mixed|mixed[] $pAppend [optional]
 * @param mixed|mixed[] $pPrepend [optional]
 * @param int $keyPosition [optional]
 * @return mixed|mixed[]
 */
function df_call_a(callable $f, array $parentArgs, $pAppend = [], $pPrepend = [], $keyPosition = 0) {
	/**
	 * 2016-11-13 We can not use @see df_args() here
	 * 2019-06-05
	 * The parent function could be called in 3 ways:
	 * 1) With a single array argument.
	 * 2) With a single scalar (non-array) argument.
	 * 3) With multiple arguments.
	 * `1 === count($parentArgs)` in the 1st and 2nd cases.
	 *  1 <> count($parentArgs) in the 3rd case.
	 * We should return an array in the 1st and 3rd cases, and a scalar result in the 2nd case.
	 */
	if (1 === count($parentArgs)) {
		// 2019-06-05 It is the 1st or the 2nd case: a single argument (a scalar or an array).
		$parentArgs = $parentArgs[0];
	}
	return
		// 2019-06-05 It is the 2nd case: a single scalar (non-array) argument
		!is_array($parentArgs)
		? call_user_func_array($f, array_merge($pPrepend, [$parentArgs], $pAppend))
		: df_map($f, $parentArgs, $pAppend, $pPrepend, $keyPosition
	);
}

/**
 * 2016-02-09
 * https://3v4l.org/iUQGl
 *	 function a($b) {return is_callable($b);}
 *	 a(function() {return 0;}); возвращает true
 * https://3v4l.org/MfmCj
 *	is_callable('intval') возвращает true
 * @used-by dfa()
 * @param mixed|callable $v
 * @param mixed[] $a [optional]
 * @return mixed
 */
function df_call_if($v, ...$a) {return
	is_callable($v) && !is_string($v) && !is_array($v) ? call_user_func_array($v, $a) : $v
;}