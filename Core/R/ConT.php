<?php
namespace Df\Core\R;
// 2017-04-01
final class ConT {
	/**
	 * 2017-04-01
	 * @used-by dfpm_c()  
	 * @used-by dfsm_c()
	 * @param bool $allowAbstract
	 * @param \Closure $f
	 * @return mixed
	 */
	static function p($allowAbstract, \Closure $f) {
		/** @var bool $prev */
		$prev = self::$allow_abstract;
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
	 * @param \Closure $f
	 * @param object|string $c
	 * @param string|string[] $suf
	 * @param string|null $def [optional]
	 * @param bool $throw [optional]
	 * @return string|null
	 */
	static function generic(\Closure $f, $c, $suf, $def = null, $throw = true) {return dfcf(
		function($f, $c, $suf, $def = null, $throw = true) {return /** @var string $result */
			df_class_exists($result = df_ctr($f($c, $suf)))
				? (self::$allow_abstract || !df_class_check_abstract($result) ? $result : (!$throw ? null :
					df_error("The «{$result}» class is abstract.")
				))
				: ($def ?: (!$throw ? null : df_error("The «{$result}» class is required.")))
		;}, [$f, df_cts($c), $suf, $def, $throw]
	);}

	/**         
	 * 2017-04-01  
	 * @used-by generic()
	 * @used-by p()
	 * @var bool
	 */
	private static $allow_abstract = false;
}