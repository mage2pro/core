<?php
/**
 * 2016-02-09
 * 2024-09-07
 * The previous solution:
 *        return is_callable($v) && !is_string($v) && !is_array($v) ? call_user_func_array($v, $a) : $v
 * https://github.com/mage2pro/core/blob/11.3.8/Core/lib/lang/call/if.php#L19-L21
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
function df_call_if($v, ...$a) {return $v instanceof Closure ? $v(...$a) : $v;}