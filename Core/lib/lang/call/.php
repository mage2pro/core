<?php
use ReflectionFunction as RF;
use ReflectionMethod as RM;
use ReflectionParameter as RP;

/**
 * 2015-12-30
 * Унифицирует вызов калбэков:
 * позволяет в качестве $f передавать как строковое название метода,
 * так и анонимную функцию, которая в качестве аргумента получит $o.
 * https://3v4l.org/pPGtA
 * 2017-07-09 Now the function can accept an array as $o.
 * 2024-09-06 "Provide an ability to pass named arguments to `df_call()`": https://github.com/mage2pro/core/issues/433
 * @used-by df_column()
 * @used-by df_each()
 * @used-by Mage_Core_Model_Layout::_generateAction() (https://github.com/thehcginstitute-com/m1/issues/676))
 * @param object|mixed|array $o
 * @param string|callable $f
 * @return mixed
 */
function df_call($o, $f, array $p = []) {/** @var mixed $r */
	if (df_is_assoc($o)) {
		$r = dfa($o, $f);
	}
	elseif ($f instanceof Closure) {
		# 2024-09-07
		# $o is passed as the first argument for $m, because in this case $m is a «getter» closure.
		# It is used by df_column() and df_each().
		$r = call_user_func_array($f, array_merge([$o], $p));
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
		if ($isMethod = is_callable([$o, $f]) && method_exists($o, $f)) {
			$callable = [$o, $f];
		}
		elseif (function_exists($f)) {
			$callable = $f;
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
				$rfa = $isMethod ? new RM($o, $f) : new RF($f); /** @var RM|RF $rfa */
				$r = $rfa->invoke(...array_merge(
					$isMethod ? [$o] : []
					,df_map($rfa->getParameters(), function(RP $rp) use($p, $rfa, $isMethod) {return dfa($p, $rp->getName(),
						# 2024-09-07
						# "`df_call()`: «Failed to retrieve the default value»":
						# https://github.com/thehcginstitute-com/m1/issues/678
						function(string $n) use($p, $rp, $rfa, $isMethod) {return $rp->isOptional() ? $rp->getDefaultValue() :
							df_error(
								sprintf(
									"`df_call()`: the required argument `{$n}` of the `{$rfa->getName()}` %s is missed."
									,$isMethod ? 'method' : 'function'
								)
								,$p
							);
						}
					);})
				));
			}
		}
		elseif (df_has_gd($o)) {
			$r = dfad($o, $f);
		}
		else {
			df_error("Unable to call «{$f}».");
		}
	}
	return $r;
}