<?php
use ReflectionFunction as RF;
use ReflectionMethod as RM;
use ReflectionParameter as RP;

/**
 * 2015-12-30
 * Унифицирует вызов калбэков:
 * позволяет в качестве $m передавать как строковое название метода,
 * так и анонимную функцию, которая в качестве аргумента получит $o.
 * https://3v4l.org/pPGtA
 * 2017-07-09 Now the function can accept an array as $object.
 * 2024-09-06 "Provide an ability to pass named arguments to `df_call()`": https://github.com/mage2pro/core/issues/433
 * @used-by df_column()
 * @used-by df_each()
 * @param object|mixed|array $o
 * @param string|callable $m
 * @return mixed
 */
function df_call($o, $m, array $p = []) {/** @var mixed $r */
	if (df_is_assoc($o)) {
		$r = dfa($o, $m);
	}
	elseif ($m instanceof Closure) {
		# 2024-09-07
		# $o is passed as the first argument for $m, because in this case $m is a «getter» closure.
		# It is used by df_column() and df_each().
		$r = call_user_func_array($m, array_merge([$o], $p));
	}
	else {
		$callable = null; /** @var ?callable $callable */
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
		/** @var bool $isMethod */
		if ($isMethod = is_callable([$o, $m]) && method_exists($o, $m)) {
			$callable = [$o, $m];
		}
		elseif (function_exists($m)) {
			$callable = $m;
			# 2024-09-07
			# $o is passed as the first argument for $m, because in this case $m is a name of a «getter» function.
			# It is used by df_column() and df_each().
			$p = array_merge([$o], $p);
		}
		if ($callable) {
			if (!df_is_assoc($p)) {
				$r = call_user_func_array($callable, $p);
			}
			else {
				# 2024-09-07
				# 1) "Provide an ability to pass named arguments to `df_call()`": https://github.com/mage2pro/core/issues/433
				# 2) https://www.php.net/manual/en/reflectionmethod.invokeargs.php#100041
				$rfa = $isMethod ? new RM($o, $m) : new RF($m); /** @var RM|RF $rfa */
				$r = $rfa->invoke(...array_merge(
					$isMethod ? [$o] : []
					,df_map($rfa->getParameters(), function(RP $rp) use($p) {return dfa(
						$p, $rp->getName(), $rp->getDefaultValue()
					);})
				));
			}
		}
		elseif (df_has_gd($o)) {
			$r = dfad($o, $m);
		}
		else {
			df_error("Unable to call «{$m}».");
		}
	}
	return $r;
}