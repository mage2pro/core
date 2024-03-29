<?php
namespace Df\Core\R;
# 2017-04-01
final class ConT {
	/**
	 * 2017-04-01
	 * @used-by dfpm_c()  
	 * @used-by dfsm_c()
	 * @return mixed
	 */
	static function p(bool $allowAbstract, \Closure $f) {
		$prev = self::$allow_abstract; /** @var bool $prev */
		self::$allow_abstract = $allowAbstract;
		try {return $f();}
		finally {self::$allow_abstract = $prev;}
	}

	/**
	 * Инструмент парадигмы «convention over configuration».
	 * 2016-10-26
	 * @used-by df_con()
	 * @used-by df_con_child()
	 * @used-by df_con_sibling()
	 * @param object|string $c
	 * @param string|string[] $suf
	 * @return string|null
	 */
	static function generic(\Closure $f, $c, $suf, string $def = '', bool $throw = true) {return dfcf(
		function($f, $c, $suf, string $def, $throw = true) {return /** @var string $r */
			df_class_exists($r = df_ctr($f($c, $suf)))
				? (self::$allow_abstract || !df_is_abstract($r) ? $r : (!$throw ? null :
					df_error("The «{$r}» class is abstract.")
				))
				: ($def ?: (!$throw ? null : df_error("The «{$r}» class is required.")))
		;}, [$f, df_cts($c), $suf, $def, $throw]
	);}

	/**         
	 * 2017-04-01  
	 * @used-by self::generic()
	 * @used-by self::p()
	 * @var bool
	 */
	private static $allow_abstract = false;
}