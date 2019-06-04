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
function df_call($o, $m, $p = []) {
	/** @var mixed $r */
	if (is_array($o) && df_is_assoc($o)) {
		$r = dfa($o, $m);
	}
	elseif (!is_string($m)) {
		// $method — инлайновая функция
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
 * @param mixed[]|mixed[][] $a
 * @param mixed|mixed[] $pAppend [optional]
 * @param mixed|mixed[] $pPrepend [optional]
 * @param int $keyPosition [optional]
 * @return mixed|mixed[]
 */
function df_call_a(callable $f, array $a, $pAppend = [], $pPrepend = [], $keyPosition = 0) {
	/**
	 * 2016-11-13 We can not use @see df_args() here
	 * 2019-06-04
	 * `1 === count($a)` means
	 * that the parent function was called with a scalar value, not with an array of values.
	 * We should return a scalar result in this case.
	 */
	/** @var bool $isScalar */
	if (($isScalar = 1 === count($a))) {
		$a = $a[0];
		// 2019-06-04
		// @todo `$a` could be still an array here:
		// it is happen when a parent function accepts multiple arguments.
		// We need to support this case: see the `call_user_func_array` call below.
	}
	return $isScalar ? call_user_func_array($f, array_merge($pPrepend, [$a], $pAppend)) : df_map(
		$f, $a, $pAppend, $pPrepend, $keyPosition
	);
}

/**
 * 2016-02-09
 * https://3v4l.org/iUQGl
 *	 function a($b) {return is_callable($b);}
 *	 a(function() {return 0;}); возвращает true
 * https://3v4l.org/MfmCj
 *	is_callable('intval') возвращает true
 * @param mixed|callable $v
 * @param mixed[] $a [optional]
 * @return mixed
 */
function df_call_if($v, ...$a) {return
	is_callable($v) && !is_string($v) && !is_array($v)
	? call_user_func_array($v, $a)
	: $v
;}