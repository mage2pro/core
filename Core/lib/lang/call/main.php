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