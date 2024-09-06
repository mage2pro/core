<?php
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