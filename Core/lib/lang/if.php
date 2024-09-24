<?php
/**
 * Осуществляет ленивое ветвление.
 * @used-by df_cfg()
 * @param mixed|callable $onTrue
 * @param mixed|null|callable $onFalse [optional]
 * @return mixed
 */
function df_if(bool $cond, $onTrue, $onFalse = null) {return $cond ? df_call_if($onTrue) : df_call_if($onFalse);}

/**
 * 2016-02-09 Осуществляет ленивое ветвление только для первой ветки.
 * @used-by df_request()
 * @param mixed|callable $onTrue
 * @param mixed|null $onFalse [optional]
 * @return mixed
 */
function df_if1(bool $cond, $onTrue, $onFalse = null) {return $cond ? df_call_if($onTrue) : $onFalse;}

/**
 * 2016-02-09 Осуществляет ленивое ветвление только для второй ветки.
 * @used-by Df\Config\Settings::p()
 * @param mixed $onTrue
 * @param mixed|null|callable $onFalse [optional]
 * @return mixed
 */
function df_if2(bool $cond, $onTrue, $onFalse = null) {return $cond ? $onTrue : df_call_if($onFalse);}