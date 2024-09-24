<?php
use Throwable as T; # 2023-08-03 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311

/**
 * 2021-10-04
 * 2023-07-27  The first entry in the result is the caller of df_bt().
 * @used-by df_bt_has()
 * @used-by df_bt_s()
 * @used-by df_caller_entry()
 * @used-by dfs_con()
 * @used-by Df\Qa\Failure\Exception::trace()
 * @used-by Df\Qa\Method::caller()
 * @param T|int|null|array(array(string => string|int)) $p [optional]
 * @return array(array(string => mixed))
 */
function df_bt($p = 0, int $limit = 0):array {
	$r = is_array($p) ? $p : (df_is_th($p) ? df_bt_th($p) :
		debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, !$limit ? 0 : 1 + $p + $limit)
	);
	# 2023-07-27 "Shift the `file` and `line` keys to an entry back in `df_bt()`": https://github.com/mage2pro/core/issues/283
	# 2024-06-06 "Use the «Symmetric array destructuring» PHP 7.1 feature": https://github.com/mage2pro/core/issues/379
	[$f, $l] = ['', 0]; /** @var string $f */ /** @var int $l */
	foreach ($r as &$e) {/** @var array(string => int|string) $e */
		[$f2, $l2] = [df_bt_entry_file($e), df_bt_entry_line($e)]; /** @var string $f2 */ /** @var int $l2 */
		$e = ['file' => $f, 'line' => $l] + $e;
		[$f, $l] = [$f2, $l2];
	}
	/**
	 * 2023-07-28 We skip the first entry: `df_bt`.
	 * 2023-08-25
	 * For the @see \Throwable case we do not need to skip df_bt() (because it is absent in the trace),
	 * but we still need to skip the first frame because it is empty after we shifted the `file` and `line` keys (see above).
	 * The first frame was artificial: @see df_bt_th()
	 */
	$r = df_slice($r, 1);
	return is_array($p) || df_is_th($p) ? $r : df_slice($r, $p, $limit);
}

/**
 * 2020-05-25
 * @used-by Df\Framework\Log\Handler\NoSuchEntity::_p()
 */
function df_bt_has(string $c, string $m = '', ?T $t = null):bool {
	list($c, $m) = $m ? [$c, $m] : explode('::', $c);
	return !!df_find(function(array $i) use($c, $m) {return $c === dfa($i, 'class') && $m === dfa($i, 'function');}, df_bt($t));
}

/**
 * 2021-10-04
 * @used-by df_bt_log()
 * @used-by df_bt_s()
 * @used-by df_caller_entry()
 * @used-by df_caller_module()
 * @param T|int|null|array(array(string => string|int)) $p
 * @return T|int
 */
function df_bt_inc($p, int $o = 1) {return is_array($p) || df_is_th($p) ? $p : $o + $p;}